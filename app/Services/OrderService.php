<?php

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\Bahan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\PemakaianBahan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// semua aturan bisnis pesanan ditaruh di sini, bukan di controller.
// nanti REST API buat APK manggil class yang sama, jadi aturannya nggak ditulis dua kali
class OrderService
{
    // $items bentuknya [menu_id => jumlah porsi], contoh: [1 => 2, 4 => 1]
    public function createOrder(array $items, int $tableNumber, string $paymentMethod, User $creator): Pesanan
    {
        // semua langkah di bawah satu paket: kalau ada yang gagal (stok kurang dll), semuanya dibatalin.
        // jadi nggak ada pesanan setengah jadi dan nggak ada stok yang kepotong sendiri
        return DB::transaction(function () use ($items, $tableNumber, $paymentMethod, $creator) {

            // 1) ambil menu + resepnya, pastiin semuanya boleh dipesan
            $menus = Menu::with('resep')->whereIn('id', array_keys($items))->get()->keyBy('id');

            if ($menus->count() !== count($items)) {
                throw new OrderException('Ada menu yang tidak ditemukan.');
            }

            foreach ($menus as $menu) {
                if (! $menu->aktif) {
                    throw new OrderException("Menu {$menu->nama} sedang nonaktif.");
                }
                if ($menu->resep->isEmpty()) {
                    throw new OrderException("Menu {$menu->nama} belum punya resep.");
                }
            }

            // 2) hitung kebutuhan bahan GABUNGAN dari semua item.
            // contoh: Latte (susu 150) + Cappuccino (susu 100) -> kebutuhan susu 250, bukan dicek satu-satu
            $needs = [];
            foreach ($items as $menuId => $qty) {
                foreach ($menus[$menuId]->resep as $r) {
                    $needs[$r->bahan_id] = round(($needs[$r->bahan_id] ?? 0) + (float) $r->takaran * $qty, 3);
                }
            }
            ksort($needs);

            // 3) kunci baris bahan dari id kecil ke besar. kalau ada dua pesanan bersamaan,
            // yang kedua nunggu yang pertama selesai, jadi stok nggak bisa kepotong dua kali.
            // urutannya selalu sama biar nggak saling tunggu (deadlock)
            $bahan = Bahan::whereIn('id', array_keys($needs))->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            // 4) cek stok. semua bahan yang kurang dikumpulin biar kasir tahu lengkap
            $shortages = [];
            foreach ($needs as $bahanId => $butuh) {
                $stok = round((float) $bahan[$bahanId]->stok, 3);

                if ($stok < $butuh) {
                    $shortages[] = "{$bahan[$bahanId]->nama} (butuh {$butuh} {$bahan[$bahanId]->satuan_dasar}, tersedia {$stok})";
                }
            }

            if (! empty($shortages)) {
                throw new OrderException('Stok tidak cukup: ' . implode('; ', $shortages) . '.');
            }

            // 5) hitung biaya, semuanya bilangan bulat rupiah (tanpa float biar nggak meleset).
            // service 5% dari subtotal, pajak 10% dari (subtotal + service), dibulatin ke rupiah terdekat
            $subtotal = 0;
            foreach ($items as $menuId => $qty) {
                $subtotal += $menus[$menuId]->harga * $qty;
            }
            $service = intdiv($subtotal * 5 + 50, 100);
            $pajak = intdiv(($subtotal + $service) * 10 + 50, 100);

            // 6) simpan pesanan. pelanggan_id kosong karena dibuat kasir (isinya cuma buat pesanan dari APK)
            $pesanan = Pesanan::create([
                'pelanggan_id' => null,
                'dibuat_oleh' => $creator->id,
                'nomor_meja' => $tableNumber,
                'metode_bayar' => $paymentMethod,
                'status' => 'baru',
                'subtotal' => $subtotal,
                'service' => $service,
                'pajak' => $pajak,
                'total' => $subtotal + $service + $pajak,
            ]);

            foreach ($items as $menuId => $qty) {
                $menu = $menus[$menuId];

                // nama_menu dan harga_satuan disalin (snapshot), jadi struk lama nggak ikut berubah
                $detail = DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $menu->id,
                    'nama_menu' => $menu->nama,
                    'harga_satuan' => $menu->harga,
                    'jumlah' => $qty,
                    'total_baris' => $menu->harga * $qty,
                ]);

                // catat bahan yang beneran dipotong, dipakai buat ngembaliin stok kalau pesanan dibatalin
                foreach ($menu->resep as $r) {
                    PemakaianBahan::create([
                        'detail_pesanan_id' => $detail->id,
                        'bahan_id' => $r->bahan_id,
                        'jumlah' => round((float) $r->takaran * $qty, 3),
                    ]);
                }
            }

            // 7) terakhir baru stok dikurangin
            foreach ($needs as $bahanId => $butuh) {
                Bahan::where('id', $bahanId)->decrement('stok', $butuh);
            }

            return $pesanan;
        });
    }

        // ubah status harus berurutan: baru -> diproses -> selesai. loncat atau mundur ditolak
    public function changeStatus(Pesanan $pesanan, string $target): Pesanan
    {
        return DB::transaction(function () use ($pesanan, $target) {
            // ambil ulang + kunci barisnya, biar dua klik bersamaan nggak bikin status loncat
            $pesanan = Pesanan::whereKey($pesanan->id)->lockForUpdate()->firstOrFail();

            // peta langkah berikutnya yang boleh dari tiap status
            $next = ['baru' => 'diproses', 'diproses' => 'selesai'];

            if (($next[$pesanan->status] ?? null) !== $target) {
                throw new OrderException("Status harus berurutan (Baru, Diproses, Selesai). Pesanan ini tidak bisa diubah dari {$pesanan->status} ke {$target}.");
            }

            $pesanan->update(['status' => $target]);

            return $pesanan;
        });
    }

    // batal cuma boleh pas status masih "baru". stok dikembalikan sebesar yang dulu BENAR-BENAR dipotong
    public function cancelOrder(Pesanan $pesanan): Pesanan
    {
        return DB::transaction(function () use ($pesanan) {
            $pesanan = Pesanan::whereKey($pesanan->id)->lockForUpdate()->firstOrFail();

            if ($pesanan->status !== 'baru') {
                throw new OrderException('Pesanan hanya bisa dibatalkan saat berstatus Baru.');
            }

            // ambil dari pemakaian_bahan (bukan dari resep sekarang), karena resep bisa aja udah diedit manajer
            $usages = PemakaianBahan::whereIn('detail_pesanan_id', $pesanan->detail()->pluck('id'))->get();

            // gabung per bahan
            $totals = [];
            foreach ($usages as $u) {
                $totals[$u->bahan_id] = ($totals[$u->bahan_id] ?? 0) + (float) $u->jumlah;
            }
            ksort($totals);

            foreach ($totals as $bahanId => $jumlah) {
                Bahan::where('id', $bahanId)->increment('stok', round($jumlah, 3));
            }

            $pesanan->update(['status' => 'dibatalkan']);

            return $pesanan;
        });
    }
}