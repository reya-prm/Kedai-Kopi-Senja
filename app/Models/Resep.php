<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = ['menu_id', 'bahan_id', 'takaran'];

    protected function casts(): array
    {
        return ['takaran' => 'decimal:3'];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }


}
