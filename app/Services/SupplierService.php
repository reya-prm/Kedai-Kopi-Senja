<?php

namespace App\Services;

use App\Models\Bahan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierService
{
    // tarik harga terbaru dari Supplier trus di simpen ke tabel bahan
    public function syncPrices(): array
    {
        $payload = $this->fetch();

        if ($payload === null) {
            return ['ok' => false, 'updated' => 0, 'skipped' => 0, 'date' => null];
        }

        $date = $this->parseDate($payload['diperbarui'] ?? null);
        $updated = 0;
        $skipped = 0;

        // kalau db error di tengah jalan, ga ada harga yang kerubah walaupun beberapa
        DB::transaction(function () use ($payload, $date, &$updated, &$skipped) {
            foreach ($payload['data'] as $item) {
                $price = $item['harga_per_satuan'] ?? null;

                if (! is_numeric($price) || $price < 0) {
                    $skipped++;
                    continue;
                }

                // buat cocokin, tapi lewat kode dulu, kalo misal buatan manual ga dari kode, cari lewat nama
                $bahan = ! empty($item['kode']) ? Bahan::where('kode', $item['kode'])->first() : null;

                if (! $bahan && ! empty($item['nama'])) {
                    $bahan = Bahan::where('kunci_nama', Bahan::makeKey($item['nama']))->first();
                }

                // satuan dasar harus sama, biar harga per gram ga nyasar jadi harga per ml
                if (! $bahan || ($item['satuan_dasar'] ?? null) !== $bahan->satuan_dasar) {
                    //di catet di log biar keliatan bahan mana yang dilewat trus kenapa
                    Log::info('Supplier: '.($item['kode'] ?? '?') . ' dilewati (bahan tidak ditemukan atau satuan beda).');
                    $skipped++;
                    continue;
                }

                $bahan->update([
                    'harga_per_satuan' => round((float) $price, 4),
                    'harga_diperbarui_pada' => $date,
                ]);
                $updated++;
            }
        });

        return ['ok' => true, 'updated' => $updated, 'skipped' => $skipped, 'date' => $date];
    }

    // manggil supplier. hasilnya array JSON kalau berhasil, null kalau gagal dalam bentuk apa pun
    private function fetch(): ?array
    {
        $url = config('services.supplier.url');
        $key = config('services.supplier.key');

        if (! $url || ! $key) {
            Log::warning('Supplier: SUPPLIER_URL atau SUPPLIER_API_KEY belum diisi.');

            return null;
        }

        try {
            $response = Http::withHeaders(['X-API-Key' => $key])
                ->acceptJson()
                ->connectTimeout(3)   // maksimal 3 detik buat nyambung
                ->timeout(5)          // maksimal 5 detik nunggu balasan
                ->get(rtrim($url, '/') . '/harga-bahan');
        } catch (\Throwable $e) {
            // kalo ada eror apapun itu bakal di anggap "layanan ga tersedia"
            // detailnya masuk log biar gampang dicari penyebabnya
            Log::warning('Supplier tidak terhubung: ' . $e->getMessage());

            return null;
        }

        // 401 (key salah), 5xx, dan sejenisnya diperlakukan sama kayak layanan mati
        if ($response->failed()) {
            Log::warning('Supplier membalas status ' . $response->status());

            return null;
        }

        $json = $response->json();

        //balasan yg ga sesuai yang diharapkan bakal dianggap gagal juga
        if (! is_array($json) || ! isset($json['data']) || ! is_array($json['data'])) {
            Log::warning('Supplier: format balasan tidak dikenali.');

            return null;
        }

        return $json;
    }

    // tanggal dari Supplier, kalau kosong atau rusak pakai tanggal sekarang
    private function parseDate($value): Carbon
    {
        try {
            return $value ? Carbon::parse($value) : now();
        } catch (\Throwable $e) {
            return now();
        }
    }
}