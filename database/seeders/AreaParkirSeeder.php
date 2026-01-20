<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AreaParkirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('area_parkir')->insert([
    [
        'kode_area'   => 'Blok A',
        'lokasi'      => 'Area Parkir Depan Gedung',
        'foto_lokasi' => null,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
        'deleted_at'  => null, // aktif
    ],
    [
        'kode_area'   => 'Blok B',
        'lokasi'      => 'Area Parkir Belakang Gedung',
        'foto_lokasi' => null,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
        'deleted_at'  => null, // aktif
    ],
    [
        'kode_area'   => 'Blok C',
        'lokasi'      => 'Area Parkir Bsemeent Sebelah Utara',
        'foto_lokasi' => null,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
        'deleted_at'  => null, // aktif
    ],
    [
        'kode_area'   => 'Blok D',
        'lokasi'      => 'Area Parkir Basement Sebelah Selatan',
        'foto_lokasi' => null,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
        'deleted_at'  => null, // aktif
    ],
    ]);

    }
}
