<?php

namespace Database\Seeders;

use App\Models\Bahan;
use App\Support\ImportHelper;
use Illuminate\Database\Seeder;

class DemoStokSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('database/data/Data_Seeder_CSV/seed_stok_demo.csv');

        foreach (ImportHelper::readCsv($file) as $row) {
            $bahan = Bahan::where('kunci_nama', Bahan::makeKey($row['nama_bahan']))->first();
            $unit = ImportHelper::unit($row['satuan']);

            // bahannya belum ada (impor belum dijalanin) atau satuannya nggak nyambung -> dilewati
            if (! $bahan || ! $unit || $unit[0] !== $bahan->satuan_dasar || ! is_numeric($row['jumlah'])) {
                $this->command->warn("Dilewati: {$row['nama_bahan']}. Pastikan impor data awal sudah dijalankan.");
                continue;
            }

            // stok DISET (bukan ditambah), jadi dijalanin berapa kali pun hasilnya sama: 0,3 liter = 300 ml
            $bahan->update(['stok' => round($row['jumlah'] * $unit[1], 3)]);
        }
    }
}