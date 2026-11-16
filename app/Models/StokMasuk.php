<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokMasuk extends Model
{
    protected $table = 'stok_masuk';

    protected $fillable = ['bahan_id', 'user_id', 'jumlah', 'tanggal'];

    protected function casts(): array
    {
        return ['jumlah' => 'decimal:3', 'tanggal' => 'date'];
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
