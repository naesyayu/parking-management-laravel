<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan database yang digunakan
        DB::statement("USE `parking-management`");

        Schema::create('detail_parkir', function (Blueprint $table) {
            $table->id('id_tarif_detail'); // AUTO_INCREMENT
            $table->decimal('jam_min', 5, 2);
            $table->decimal('jam_max', 5, 2);

            // Engine & charset
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
        Schema::dropIfExists('detail_parkir');
    }
};
