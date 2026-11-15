<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AkunStafSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [
            ['nama' => 'Manajer Senja', 'peran' => 'manajer', 'email' => 'manajer@senja.test', 'password' => 'manajer123'],
            ['nama' => 'Kasir Senja', 'peran' => 'kasir', 'email' => 'kasir@senja.test', 'password' => 'kasir12345'],
        ];

        foreach ($akun as $data){
            //kalo email udah ada datanya di perbarui, kalo belum dibuat baru. jadi seeder nya aman buat dijalanin berkalikali tanpa double akun nantinya
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
