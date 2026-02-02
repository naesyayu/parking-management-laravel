<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRolesWithPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // UPDATE ROLES DENGAN PERMISSIONS
        // ==========================================
        
        // Owner - Akses Semua
        DB::table('roles')->where('id_role', 1)->update([
            'permissions' => json_encode([
                'transaksi' => true,
                'master_data' => false,
                'laporan' => true,
                'activity_log' => false,
                'user_management' => false,
            ]),
            'updated_at' => now(),
        ]);

        // Admin - Semua kecuali Transaksi
        DB::table('roles')->where('id_role', 2)->update([
            'permissions' => json_encode([
                'transaksi' => false,
                'master_data' => true,
                'laporan' => true,
                'activity_log' => true,
                'user_management' => true,
            ]),
            'updated_at' => now(),
        ]);

        // Petugas Parkir - Hanya Transaksi
        DB::table('roles')->where('id_role', 3)->update([
            'permissions' => json_encode([
                'transaksi' => true,
                'master_data' => false,
                'laporan' => false,
                'activity_log' => false,
                'user_management' => false,
            ]),
            'updated_at' => now(),
        ]);

    }
}