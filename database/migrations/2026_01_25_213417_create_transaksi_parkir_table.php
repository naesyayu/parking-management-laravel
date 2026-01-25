<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    
        DB::statement("USE `parking-management`");

        Schema::create('transaksi_parkir', function (Blueprint $table) {

            $table->id('id_transaksi');

            $table->unsignedBigInteger('kode_tiket');

            $table->unsignedInteger('id_kendaraan');
            $table->unsignedInteger('id_area');

            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar');

            $table->decimal('durasi_jam', 6, 2);

            $table->unsignedInteger('id_tarif');
            $table->unsignedInteger('id_user')->nullable();
            $table->unsignedInteger('id_member')->nullable();

            /**
             * 🔥 METODE PEMBAYARAN (FK)
             */
            $table->unsignedBigInteger('id_metode');

            /**
             * Status kendaraan
             */
            $table->enum('status', ['in', 'out'])->default('in');

            $table->softDeletes();
            $table->timestamps();

            /**
             * =========================
             * FOREIGN KEY CONSTRAINTS
             * =========================
             */

            $table->foreign('id_kendaraan')
                ->references('id_kendaraan')
                ->on('kendaraan')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_area')
                ->references('id_area')
                ->on('area_parkir')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_tarif')
                ->references('id_tarif')
                ->on('tarif_parkir')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_member')
                ->references('id_member')
                ->on('member')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_metode')
                ->references('id_metode')
                ->on('metode_pembayaran')
                ->onUpdate('cascade')
                ->onDelete('restrict');
                
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_parkir');
    }
};