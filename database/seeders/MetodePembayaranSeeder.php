<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodePembayaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('metode_pembayaran')->insert([
            [
                'metode_bayar' => 'Tunai',
                'deleted_at'   => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'metode_bayar' => 'QRIS',
                'deleted_at'   => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'metode_bayar' => 'E-Wallet',
                'deleted_at'   => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'metode_bayar' => 'Transfer Bank Mandiri',
                'deleted_at'   => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'metode_bayar' => 'Transfer Bank BRI',
                'deleted_at'   => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'metode_bayar' => 'Transfer Bank BCA',
                'deleted_at'   => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}