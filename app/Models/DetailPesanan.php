<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $fillable = ['pesanan_id', 'menu_id', 'nama_menu', 'harga_satuan', 'jumlah', 'total_baris'];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function pemakaianBahan(): HasMany
    {
        return $this->hasMany(PemakaianBahan::class, 'detail_pesanan_id');
    }
}
