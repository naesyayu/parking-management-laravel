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
        
        Schema::create('pemilik', function (Blueprint $table) {
            $table->id('id_pemilik'); // Primary key dengan nama id_pemilik
            $table->string('nama', 100);
            $table->string('no_hp', 20);
            $table->text('alamat');
            
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
        Schema::dropIfExists('pemilik');
    }
};
