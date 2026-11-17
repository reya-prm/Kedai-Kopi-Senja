<?php

namespace App\Support;

use RuntimeException;

// alat bantu yang dipakai bareng sama command impor dan seeder demo
class ImportHelper
{
    // satuan di CSV -> [satuan dasar, pengali]. 1 kg = 1000 g, 1 liter = 1000 ml
    private const UNITS = [
        'kg' => ['g', 1000],
        'g' => ['g', 1],
        'liter' => ['ml', 1000],
        'ml' => ['ml', 1],
        'pcs' => ['pcs', 1],
    ];

    // balikin [satuan dasar, pengali], atau null kalau satuannya nggak dikenal
    public static function unit(string $unit): ?array
    {
        return self::UNITS[strtolower(trim($unit))] ?? null;
    }

    // baca CSV jadi daftar baris, tiap baris berbentuk ['nama_kolom' => nilai]
    public static function readCsv(string $file): array
    {
        if (! is_file($file)) {
            throw new RuntimeException("File tidak ditemukan: {$file}");
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle, null, ',', '"', '');

        // buang BOM (karakter tak terlihat di awal file hasil Excel) dan spasi di nama kolom
        $header = array_map(fn ($h) => trim(ltrim($h, "\xEF\xBB\xBF")), $header);

        $rows = [];
        while (($line = fgetcsv($handle, null, ',', '"', '')) !== false) {
            // baris kosong ([null]) dan baris yang jumlah kolomnya nggak pas dilewati
            if ($line === [null] || count($line) !== count($header)) {
                continue;
            }
            $rows[] = array_combine($header, array_map('trim', $line));
        }
        fclose($handle);

        return $rows;
    }
}