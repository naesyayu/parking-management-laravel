<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("USE `parking-management`");

        Schema::create('member', function (Blueprint $table) {
            $table->id('id_member');

            $table->unsignedBigInteger('id_pemilik');
            $table->unsignedBigInteger('id_level');

            $table->date('berlaku_mulai');
            $table->date('berlaku_hingga');

            $table->enum('status', ['aktif', 'expired'])
                  ->default('aktif')
                  ->collation('utf8mb4_0900_ai_ci');

            $table->softDeletes(); // deleted_at
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_pemilik')
                  ->references('id_pemilik')
                  ->on('pemilik')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('id_level')
                  ->references('id_level')
                  ->on('member_level')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Engine & charset
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member');
    }
};
