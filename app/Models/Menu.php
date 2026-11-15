<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = ['nama', 'kategori', 'harga', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function resep(): HasMany
    {
        return $this->hasMany(Resep::class, 'menu_id');
    }

    //
    public function bahan(): BelongsToMany
    {
        return $this->belongsToMany(Bahan::class, 'resep', 'menu_id', 'bahan_id')->withPivot('takaran');
    }
}
