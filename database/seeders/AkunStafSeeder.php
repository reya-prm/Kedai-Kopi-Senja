<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AkunStafSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Manajer Senja', 'role' => 'manajer', 'username' => 'manajer', 'password' => 'manajer123'],
            ['name' => 'Kasir Senja', 'role' => 'kasir', 'username' => 'kasir', 'password' => 'kasir12345'],
        ];

        foreach ($accounts as $data) {
            // dicari lewat username: kalau udah ada diperbarui, kalau belum dibuat.
            // jadi aman dijalanin berkali-kali tanpa bikin akun dobel
            User::updateOrCreate(['username' => $data['username']], $data);
        }
    }
}