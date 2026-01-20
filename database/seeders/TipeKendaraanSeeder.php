<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipeKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipe_kendaraan')->insert([
            [
                'tipe_kendaraan' => 'motor',
            ],
            [
                'tipe_kendaraan' => 'mobil',
            ],
            [
                'tipe_kendaraan' => 'elf',
            ],
            [
                'tipe_kendaraan' => 'bus',
            ],
            [
                'tipe_kendaraan' => 'truk',
            ],
    
        ]);
    }
}
