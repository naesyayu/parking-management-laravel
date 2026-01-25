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

        Schema::create('tarif_parkir', function (Blueprint $table) {
            $table->id('id_tarif');

            $table->unsignedBigInteger('id_tarif_detail');
            $table->unsignedBigInteger('id_tipe');

            $table->integer('tarif');

            $table->softDeletes(); // deleted_at
            $table->timestamps();  // created_at & updated_at

            /* Foreign Key */
            $table->foreign('id_tarif_detail')
                  ->references('id_tarif_detail')
                  ->on('detail_parkir')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('id_tipe')
                  ->references('id_tipe')
                  ->on('tipe_kendaraan')
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
        Schema::dropIfExists('tarif_parkir');
    }
};
