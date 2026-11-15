<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Override;

class User extends Authenticatable
{
    protected $table = 'pengguna';

    protected $fillable = ['nama', 'peran', 'email', 'nomor_hp', 'password', 'wajib_ganti_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return[
            'password' => 'hashed',     //otomastis hash
            'wajib_ganti_password' => 'boolean',
        ];
    }
}
