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

        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id('id_kendaraan');

            $table->string('plat_nomor', 9)->unique();

            $table->unsignedBigInteger('id_pemilik')->nullable();
            $table->unsignedBigInteger('id_tipe');

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

            $table->softDeletes();
            $table->timestamps();

            /*
            |----------------------------------
            | Foreign Key Constraints
            |----------------------------------
            */
            $table->foreign('id_pemilik')
                ->references('id_pemilik')
                ->on('pemilik')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('id_tipe')
                ->references('id_tipe')
                ->on('tipe_kendaraan')
                ->onDelete('restrict')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('kendaraan');
    }
};
