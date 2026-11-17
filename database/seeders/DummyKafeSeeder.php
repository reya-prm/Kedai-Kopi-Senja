<?php

namespace Database\Seeders;

use App\Models\Bahan;
use App\Models\Menu;
use App\Models\Resep;
use Illuminate\Database\Seeder;

class DummyKafeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data dummy bahan (sudah dalam satuan dasar g, ml, pcs + harga per satuan dasar)
        $daftarBahan = [
            ['nama' => 'Biji kopi arabika', 'satuan_dasar' => 'g', 'stok' => 5000, 'batas_minimum' => 500, 'harga_per_satuan' => 180],
            ['nama' => 'Susu UHT', 'satuan_dasar' => 'ml', 'stok' => 10000, 'batas_minimum' => 1000, 'harga_per_satuan' => 18],
            ['nama' => 'Gula aren cair', 'satuan_dasar' => 'ml', 'stok' => 2000, 'batas_minimum' => 300, 'harga_per_satuan' => 45],
            ['nama' => 'Gelas 16 oz', 'satuan_dasar' => 'pcs', 'stok' => 200, 'batas_minimum' => 30, 'harga_per_satuan' => 550],
            ['nama' => 'Tutup gelas', 'satuan_dasar' => 'pcs', 'stok' => 200, 'batas_minimum' => 30, 'harga_per_satuan' => 150],
            ['nama' => 'Roti tawar', 'satuan_dasar' => 'pcs', 'stok' => 40, 'batas_minimum' => 10, 'harga_per_satuan' => 1500],
            ['nama' => 'Keju slice', 'satuan_dasar' => 'pcs', 'stok' => 50, 'batas_minimum' => 10, 'harga_per_satuan' => 2500],
        ];

        $bahanMap = [];
        foreach ($daftarBahan as $item) {
            $kunci = Bahan::makeKey($item['nama']);
            $bahan = Bahan::where('kunci_nama', $kunci)->first();

            if (! $bahan) {
                $bahan = new Bahan();
                $bahan->nama = $item['nama'];
                $bahan->satuan_dasar = $item['satuan_dasar'];
            }

            $bahan->stok = $item['stok'];
            $bahan->batas_minimum = $item['batas_minimum'];
            $bahan->harga_per_satuan = $item['harga_per_satuan'];
            $bahan->harga_diperbarui_pada = now();
            $bahan->save();

            $bahanMap[$item['nama']] = $bahan;
        }

        // 2. Data dummy menu
        $daftarMenu = [
            ['nama' => 'Latte', 'kategori' => 'Minuman', 'harga' => 28000, 'aktif' => true],
            ['nama' => 'Kopi Susu Gula Aren', 'kategori' => 'Minuman', 'harga' => 26000, 'aktif' => true],
            ['nama' => 'Roti Bakar Keju', 'kategori' => 'Makanan', 'harga' => 22000, 'aktif' => true],
            ['nama' => 'Menu Baru Tanpa Resep', 'kategori' => 'Minuman', 'harga' => 20000, 'aktif' => false],
        ];

        $menuMap = [];
        foreach ($daftarMenu as $item) {
            $menu = Menu::where('nama', $item['nama'])->first();
            if (! $menu) {
                $menu = new Menu();
            }

            $menu->nama = $item['nama'];
            $menu->kategori = $item['kategori'];
            $menu->harga = $item['harga'];
            $menu->aktif = $item['aktif'];
            $menu->save();

            $menuMap[$item['nama']] = $menu;
        }

        // 3. Data dummy resep
        $daftarResep = [
            // Latte: HPP = (18*180) + (150*18) + (1*550) + (1*150) = 3240 + 2700 + 550 + 150 = 6640
            ['menu' => 'Latte', 'bahan' => 'Biji kopi arabika', 'takaran' => 18],
            ['menu' => 'Latte', 'bahan' => 'Susu UHT', 'takaran' => 150],
            ['menu' => 'Latte', 'bahan' => 'Gelas 16 oz', 'takaran' => 1],
            ['menu' => 'Latte', 'bahan' => 'Tutup gelas', 'takaran' => 1],

            // Kopi Susu Gula Aren: HPP = (18*180) + (100*18) + (20*45) + (1*550) + (1*150) = 6640
            ['menu' => 'Kopi Susu Gula Aren', 'bahan' => 'Biji kopi arabika', 'takaran' => 18],
            ['menu' => 'Kopi Susu Gula Aren', 'bahan' => 'Susu UHT', 'takaran' => 100],
            ['menu' => 'Kopi Susu Gula Aren', 'bahan' => 'Gula aren cair', 'takaran' => 20],
            ['menu' => 'Kopi Susu Gula Aren', 'bahan' => 'Gelas 16 oz', 'takaran' => 1],
            ['menu' => 'Kopi Susu Gula Aren', 'bahan' => 'Tutup gelas', 'takaran' => 1],

            // Roti Bakar Keju: HPP = (3*1500) + (2*2500) = 4500 + 5000 = 9500
            ['menu' => 'Roti Bakar Keju', 'bahan' => 'Roti tawar', 'takaran' => 3],
            ['menu' => 'Roti Bakar Keju', 'bahan' => 'Keju slice', 'takaran' => 2],
        ];

        foreach ($daftarResep as $item) {
            $menu = $menuMap[$item['menu']];
            $bahan = $bahanMap[$item['bahan']];

            $resep = Resep::where('menu_id', $menu->id)
                ->where('bahan_id', $bahan->id)
                ->first();

            if (! $resep) {
                $resep = new Resep();
                $resep->menu_id = $menu->id;
                $resep->bahan_id = $bahan->id;
            }

            $resep->takaran = $item['takaran'];
            $resep->save();
        }
    }
}