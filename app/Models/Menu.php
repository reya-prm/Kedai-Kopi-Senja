<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    // nama tabelnya "menu" (nggak jamak), jadi harus ditulis sendiri
    protected $table = 'menu';

    protected $fillable = ['nama', 'kategori', 'harga', 'aktif'];

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
}