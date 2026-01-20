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
        DB::statement("USE `parking-management`");

        Schema::create('area_kapasitas', function (Blueprint $table) {
            $table->id('id_kapasitas');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('id_tipe');
            $table->integer('kapasitas');

            // Engine & charset
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';

            // Foreign Key
            $table->foreign('id_area')
                  ->references('id_area')
                  ->on('area_parkir')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('id_tipe')
                  ->references('id_tipe')
                  ->on('tipe_kendaraan')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_kapasitas');
    }
};
