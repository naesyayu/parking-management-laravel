<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailParkirSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('detail_parkir')->insert([
            [
                'jam_min' => 0.01,
                'jam_max' => 5.00,
            ],
            [
                'jam_min' => 5.01,
                'jam_max' => 6.00,
            ],
            [
                'jam_min' => 6.01,
                'jam_max' => 7.00,
            ],
            [
                'jam_min' => 7.01,
                'jam_max' => 8.00,
            ],
            [
                'jam_min' => 8.01,
                'jam_max' => 9.00,
            ],
            [
                'jam_min' => 9.01,
                'jam_max' => 10.00,
            ],
            [
                'jam_min' => 10.01,
                'jam_max' => 24.00,
            ],
            [
                'jam_min' => 05.01,
                'jam_max' => 10.00,
            ],
        ]);
    }
}
