<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'role', 'username', 'phone', 'email', 'password', 'must_change_password',
    ];

    // nggak ikut keluar waktu model diubah jadi JSON (penting buat REST API nanti)
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',                 // otomatis di-hash tiap diisi
            'must_change_password' => 'boolean',
        ];
    }
}