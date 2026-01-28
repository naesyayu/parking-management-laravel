<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PemilikSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pemilik')->insert([
            [
                'nama'   => 'Budi Santoso',
                'no_hp'  => '081234567890',
                'alamat' => 'Jl. Merdeka No. 10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'   => 'Siti Aminah',
                'no_hp'  => '082345678901',
                'alamat' => 'Jl. Sudirman No. 25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'   => 'Andi Wijaya',
                'no_hp'  => '083456789012',
                'alamat' => 'Jl. Gatot Subroto No. 5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
    }
}
