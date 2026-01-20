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
        
        Schema::create('area_parkir', function (Blueprint $table) {
            $table->id('id_area'); // Primary key dengan nama id_area
            $table->string('kode_area', 10)->unique(); // VARCHAR(10) -> string(10)
            $table->text('lokasi')->nullable(); // TEXT, nullable
            $table->string('foto_lokasi', 300)->nullable();
            $table->softDeletes();
            $table->timestamps();
            
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
        Schema::dropIfExists('area_parkir');
    }
};
