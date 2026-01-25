<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipe_kendaraan', function (Blueprint $table) {
            // Kolom kode_kendaraan sebelum tipe_kendaraan
            $table->string('kode_kendaraan', 4)
                  ->before('tipe_kendaraan')
                  ->unique();

            // Kolom deskripsi_kendaraan setelah tipe_kendaraan
            $table->string('deskripsi_kendaraan', 100)
                  ->after('tipe_kendaraan')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipe_kendaraan', function (Blueprint $table) {
            $table->dropColumn([
                'kode_kendaraan',
                'deskripsi_kendaraan'
            ]);
        });
    }
};