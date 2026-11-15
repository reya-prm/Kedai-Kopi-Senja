<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Bahan extends Model
{
    protected $table = 'bahan';

    protected $fillable = [
        'nama', 'kunci_nama', 'satuan_dasar', 'stok', 'batas_minimum', 'harga_per_satuan', 'harga_diperbarui_pada',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:3',
            'batas_minimum' => 'decimal:3',
            'harga_per_satuan' => 'decimal:4',
            'harga_diperbarui_pada' => 'datetime',
        ];
    }

    //otomatis tiap bahan disimpan 'kunci_nama' singkron trus sama nama
    #[Override]
    protected static function booted(): void
    {
        static::saving(function(Bahan $bahan) {
            $bahan->kunci_nama = self::buatKunci($bahan->nama);
        });
    }

    // "Susu UHT " dan "susu uht" menghasilkan kunci yang sama -> ditolak oleh unique di DB.
    public static function buatKunci(string $nama):string
    {
        return mb_strtolower(trim($nama));
    }

    public function resep(): HasMany
    {
        return $this->hasMany(Resep::class, 'bahan_id');
    }

    public function stokMasuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class, 'bahan_id');
    }
}
