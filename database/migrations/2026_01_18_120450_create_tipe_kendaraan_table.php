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
        // Set database yang akan digunakan
        DB::statement("USE `parking-management`");
        
        Schema::create('tipe_kendaraan', function (Blueprint $table) {
            $table->id('id_tipe'); // Primary key dengan nama id_tipe
            $table->string('tipe_kendaraan', 50)->unique(); // Kolom tipe_kendaraan dengan panjang 50 karakter, unique
            
            // Set engine dan charset
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipe_kendaraan');
    }
};
