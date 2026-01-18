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
        
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user'); // Primary key dengan nama id_user
            $table->string('username', 50)->unique()->collation('utf8mb4_0900_ai_ci');
            $table->string('password', 255)->collation('utf8mb4_0900_ai_ci');
            $table->enum('role', ['admin', 'petugas', 'owner'])->default('petugas')->collation('utf8mb4_0900_ai_ci');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->collation('utf8mb4_0900_ai_ci');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->default(null);
            
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
        Schema::dropIfExists('users');
    }
};
