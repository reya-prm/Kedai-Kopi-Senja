<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class PemakaianBahan extends Model
{
    protected $table = 'pemakaian_bahan';
    
    protected $fillable = ['detail_pesanan_id', 'bahan_id', 'jumlah'];

    protected function casts(): array
    {
        return ['jumlah' => 'decimal:3'];
    }

    public function detailPesanan(): BelongsTo
    {
        return $this->belongsTo(DetailPesanan::class, 'detail_pesanan_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
