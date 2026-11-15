<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {   
        //panggil akun staf dari seeder AkunStaf 
        $this->call(AkunStafSeeder::class);
    }
}
