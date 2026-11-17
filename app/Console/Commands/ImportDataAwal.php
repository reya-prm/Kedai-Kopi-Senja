<?php

namespace App\Console\Commands;

use App\Models\Bahan;
use App\Models\Menu;
use App\Models\Resep;
use App\Support\ImportHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ImportDataAwal extends Command
{
    protected $signature = 'import:data-awal {--path=database/data/Data_Awal : Folder berisi bahan.csv, menu.csv, resep.csv}';

    protected $description = 'Impor data awal bahan, menu, dan resep dari berkas CSV';

    // hitungan hasil impor, ditampilin di akhir biar kelihatan apa yang terjadi
    private array $report = ['bahan' => 0, 'menu' => 0, 'resep' => 0, 'diaktifkan' => 0];

    // catatan: typo yang dikoreksi dan baris yang dilewati
    private array $notes = [];

    // menu yang baru dibuat di impor ini (dipakai buat nentuin siapa yang diaktifin)
    private array $newMenuIds = [];

    public function handle(): int
    {
        $dir = base_path($this->option('path'));

        try {
            // semua dibungkus satu transaksi: kalau ada yang gagal di tengah, nggak ada data setengah jadi
            DB::transaction(function () use ($dir) {
                $this->importBahan(ImportHelper::readCsv("{$dir}/bahan.csv"));
                $this->importMenu(ImportHelper::readCsv("{$dir}/menu.csv"));
                $this->importResep(ImportHelper::readCsv("{$dir}/resep.csv"));
            });
        } catch (\Throwable $e) {
            $this->error('Impor dibatalkan: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info('Impor selesai.');
        $this->table(['Data', 'Jumlah'], [
            ['Bahan baru', $this->report['bahan']],
            ['Menu baru', $this->report['menu']],
            ['Resep baru', $this->report['resep']],
            ['Menu diaktifkan (punya resep)', $this->report['diaktifkan']],
        ]);

        foreach ($this->notes as $note) {
            $this->line(' - ' . $note);
        }

        return self::SUCCESS;
    }

    private function importBahan(array $rows): void
    {
        foreach ($rows as $row) {
            $unit = ImportHelper::unit($row['satuan']);

            if (! $unit || $row['kode'] === '' || $row['nama_bahan'] === ''
                || ! is_numeric($row['stok_awal']) || ! is_numeric($row['harga_per_satuan'])
                || $row['stok_awal'] < 0 || $row['harga_per_satuan'] < 0) {
                $this->notes[] = "Bahan {$row['kode']} dilewati: data tidak valid.";
                continue;
            }

            // kode sama = udah pernah diimpor. dibiarin biar stok yang udah berjalan nggak ketimpa
            if (Bahan::where('kode', $row['kode'])->exists()) {
                continue;
            }

            // nama udah dipakai bahan lain (misal buatan manual), dilewati biar nggak bentrok
            if (Bahan::where('kunci_nama', Bahan::makeKey($row['nama_bahan']))->exists()) {
                $this->notes[] = "Bahan {$row['nama_bahan']} dilewati: nama sudah dipakai bahan lain.";
                continue;
            }

            [$baseUnit, $factor] = $unit;

            $bahan = new Bahan;
            $bahan->fill([
                'kode' => $row['kode'],
                'nama' => $row['nama_bahan'],
                'satuan_dasar' => $baseUnit,
                // 5 kg jadi 5000 g (dikali), harga per kg jadi harga per g (dibagi)
                'stok' => round($row['stok_awal'] * $factor, 3),
                'harga_per_satuan' => round($row['harga_per_satuan'] / $factor, 4),
                'batas_minimum' => 0, // sesuai spesifikasi, awalnya 0
                'harga_diperbarui_pada' => now(),
            ]);
            // pakai save() (bukan update massal) biar kunci_nama keisi otomatis lewat event di model
            $bahan->save();

            $this->report['bahan']++;
        }
    }

    private function importMenu(array $rows): void
    {
        foreach ($rows as $row) {
            if ($row['kode'] === '' || $row['nama_menu'] === '' || ! ctype_digit($row['harga_jual'])) {
                $this->notes[] = "Menu {$row['kode']} dilewati: data tidak valid.";
                continue;
            }

            if (Menu::where('kode', $row['kode'])->exists()) {
                continue;
            }

            $menu = Menu::create([
                'kode' => $row['kode'],
                'nama' => $row['nama_menu'],
                'kategori' => $row['kategori'] !== '' ? $row['kategori'] : null,
                'harga' => (int) $row['harga_jual'],
                // menu baru selalu nonaktif dulu, diaktifin di akhir kalau ternyata punya resep
                'aktif' => false,
            ]);

            $this->newMenuIds[] = $menu->id;
            $this->report['menu']++;
        }
    }

    private function importResep(array $rows): void
    {
        $allBahan = Bahan::all(); // buat dicocokin namanya
        $menus = Menu::whereNotNull('kode')->get()->keyBy('kode');

        foreach ($rows as $row) {
            $menu = $menus[$row['kode_menu']] ?? null;
            $unit = ImportHelper::unit($row['satuan']);

            if (! $menu || ! $unit || ! is_numeric($row['takaran']) || $row['takaran'] <= 0) {
                $this->notes[] = "Resep {$row['kode_menu']} / {$row['nama_bahan']} dilewati: data tidak valid.";
                continue;
            }

            $bahan = $this->findBahan($row['nama_bahan'], $allBahan);

            if (! $bahan) {
                // bahan nggak terdaftar di bahan.csv: nggak diimpor (sesuai spesifikasi)
                $this->notes[] = "Resep {$row['kode_menu']}: bahan \"{$row['nama_bahan']}\" tidak terdaftar, tidak diimpor.";
                continue;
            }

            [$baseUnit, $factor] = $unit;

            // satuan di resep harus nyambung sama satuan dasar bahan (g buat bahan g, dst)
            if ($baseUnit !== $bahan->satuan_dasar) {
                $this->notes[] = "Resep {$row['kode_menu']}: satuan \"{$row['satuan']}\" tidak cocok dengan bahan {$bahan->nama}, dilewati.";
                continue;
            }

            if (Resep::where('menu_id', $menu->id)->where('bahan_id', $bahan->id)->exists()) {
                continue;
            }

            Resep::create([
                'menu_id' => $menu->id,
                'bahan_id' => $bahan->id,
                'takaran' => round($row['takaran'] * $factor, 3),
            ]);
            $this->report['resep']++;

            // namanya beda dari nama asli bahan = hasil koreksi typo
            if ($bahan->kunci_nama !== Bahan::makeKey($row['nama_bahan'])) {
                $this->notes[] = "Resep {$row['kode_menu']}: \"{$row['nama_bahan']}\" dikoreksi menjadi \"{$bahan->nama}\".";
            }
        }

        // menu baru yang ternyata punya resep diaktifin biar bisa dipesan
        foreach ($this->newMenuIds as $id) {
            if (Resep::where('menu_id', $id)->exists()) {
                Menu::where('id', $id)->update(['aktif' => true]);
                $this->report['diaktifkan']++;
            }
        }
    }

    // cocokin nama bahan di resep ke daftar bahan. null = nggak ketemu
    private function findBahan(string $name, Collection $allBahan): ?Bahan
    {
        // huruf kecil, spasi pinggir dibuang, spasi dobel dirapiin
        $key = preg_replace('/\s+/', ' ', Bahan::makeKey($name));

        // 1) cocok persis dulu
        foreach ($allBahan as $b) {
            if ($b->kunci_nama === $key) {
                return $b;
            }
        }

        // 2) kalau nggak ada, cari yang paling mirip (typo). jarak Levenshtein = jumlah huruf
        // yang harus diubah/ditambah/dihapus biar dua nama jadi sama. dicari yang terdekat dan runner-up-nya
        $best = null;
        $bestDist = PHP_INT_MAX;
        $secondDist = PHP_INT_MAX;

        foreach ($allBahan as $b) {
            $dist = levenshtein($key, $b->kunci_nama);

            if ($dist < $bestDist) {
                $secondDist = $bestDist;
                $bestDist = $dist;
                $best = $b;
            } elseif ($dist < $secondDist) {
                $secondDist = $dist;
            }
        }

        if (! $best) {
            return null;
        }

        $similarity = 1 - $bestDist / max(strlen($key), strlen($best->kunci_nama));

        // angka di nama harus sama: "Gelas 12 oz" dan "Gelas 16 oz" itu dua bahan beda, bukan typo
        $sameDigits = preg_replace('/\D/', '', $key) === preg_replace('/\D/', '', $best->kunci_nama);

        // diterima kalau: beda maksimal 2 huruf, mirip minimal 90%, angka sama, dan kandidat terbaiknya cuma satu.
        // syarat 90% ini yang bikin "Susu oat" (75%) nggak salah dikira "Susu UHT"
        if ($bestDist <= 2 && $similarity >= 0.9 && $sameDigits && $secondDist > $bestDist) {
            return $best;
        }

        return null;
    }
}