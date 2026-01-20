<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'role_user' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_user' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_user' => 'petugas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
