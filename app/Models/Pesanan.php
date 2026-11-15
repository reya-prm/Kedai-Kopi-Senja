<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [ 'pelanggan_id', 'dibuat_oleh', 'nomor_meja', 'metode_bayar','status', 'subtotal', 'service', 'pajak', 'total',];

      // Dua relasi ke tabel pengguna: pemilik akun vs yang menekan tombol "buat pesanan".
        public function pelanggan(): BelongsTo
        {
            return $this->belongsTo(User::class, 'pelanggan_id');
        }

        public function pembuat(): BelongsTo
        {
        return $this->belongsTo(User::class, 'dibuat_oleh');
        }

        public function detail(): HasMany
        {
            return $this->hasMany(DetailPesanan::class, 'pesanan_id');
        }

}
