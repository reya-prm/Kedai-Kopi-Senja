<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyesuaianStok extends Model
{
    protected $table = 'penyesuaian_stok';

    protected $fillable = [
        'bahan_id',
        'user_id',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:3',
            'stok_sebelum' => 'decimal:3',
            'stok_sesudah' => 'decimal:3',
        ];
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
