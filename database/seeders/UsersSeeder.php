<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
    [
        'username' => 'owner1',
        'password' => Hash::make('password123'),
        'id_role' => 1,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'username' => 'admin1',
        'password' => Hash::make('password123'),
        'id_role' => 2,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'username' => 'petugas1',
        'password' => Hash::make('password123'),
        'id_role' => 3,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

    }
}
