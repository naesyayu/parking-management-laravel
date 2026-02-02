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
        DB::statement("USE `parking-management`");
        
        Schema::table('transaksi_parkir', function (Blueprint $table) {
            // Tambahkan kolom diskon dan total_bayar setelah kolom id_tarif
            $table->decimal('diskon', 10, 2)->default(0)->after('id_tarif');
            
            $table->decimal('total_bayar', 10, 2)->default(0)->after('diskon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_parkir', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'total_bayar']);
        });
    }
};
