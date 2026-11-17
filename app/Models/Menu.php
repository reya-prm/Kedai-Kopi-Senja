<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use phpDocumentor\Reflection\Types\Float_;

class Menu extends Model
{
    // nama tabelnya "menu" (nggak jamak), jadi harus ditulis sendiri
    protected $table = 'menu';

    protected $fillable = ['kode','nama', 'kategori', 'harga', 'aktif'];

    protected function casts(): array
    {
        // biar nilai aktif dibaca sebagai true/false, bukan angka 1/0
        return ['aktif' => 'boolean'];
    }

    // satu menu punya banyak baris resep (bahan + takaran)
    public function resep(): HasMany
    {
        return $this->hasMany(Resep::class, 'menu_id');
    }

    // status buat halaman kasir: tersedia / habis / nonaktif / tanpa_resep.
    // DIHITUNG tiap halaman dibuka, nggak disimpan di database, jadi otomatis ikut stok terbaru.
    public function getSalesStatusAttribute(): string
    {
        if(! $this->aktif) {
            return 'nonaktif';
        }

        if($this->resep->isEmpty()){
            return 'tanpa_resep';
        }

        // satu bahan aja yang stoknya kurang dari takaran 1 porsi, menu langsung Habis
        foreach ($this->resep as $r){
            if (round((float) $r->bahan->stok, 3) < round((float) $r->takaran, 3)){
                return 'habis';
            }
        }

        return 'tersedia';
    }

        // menu yang boleh ditampilin ke kasir: aktif DAN udah punya resep.
    // yang nonaktif atau tanpa resep nggak muncul sama sekali
    public function scopeForSale($query)
    {
        return $query->where('aktif', true)->has('resep');
    }
}