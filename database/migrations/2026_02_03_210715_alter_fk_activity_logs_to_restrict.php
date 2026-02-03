<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {

            // BUAT FK BARU (karena sebelumnya BELUM ADA)
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_transaksi')
                ->references('id_transaksi')
                ->on('transaksi_parkir')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {

            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_transaksi']);
        });
    }
};
