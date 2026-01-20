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
        // Gunakan database
        DB::statement("USE `parking-management`");

        Schema::create('member_level', function (Blueprint $table) {
            $table->id('id_level'); // Primary key AUTO_INCREMENT
            $table->string('nama_level', 20)->collation('utf8mb4_0900_ai_ci');
            $table->decimal('diskon_persen', 5, 2)
                  ->comment('Diskon dalam persen');

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
        Schema::dropIfExists('member_level');
    }
};
