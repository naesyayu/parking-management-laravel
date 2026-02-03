<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('tarif_parkir', function (Blueprint $table) {

            // DROP foreign key lama
            $table->dropForeign(['id_tarif_detail']);
            $table->dropForeign(['id_tipe']);

            // ADD foreign key baru → RESTRICT
            $table->foreign('id_tarif_detail')
                ->references('id_tarif_detail')
                ->on('detail_parkir')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_tipe')
                ->references('id_tipe')
                ->on('tipe_kendaraan')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tarif_parkir', function (Blueprint $table) {

            $table->dropForeign(['id_tarif_detail']);
            $table->dropForeign(['id_tipe']);

            // rollback ke kondisi lama
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
        });
    }
};
