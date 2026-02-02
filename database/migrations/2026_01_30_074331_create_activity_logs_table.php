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
        
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('id_log');
            
            // User yang melakukan aktivitas
            $table->unsignedBigInteger('id_user')->nullable();
            
            // Jenis aktivitas
            $table->enum('action', [
                'login',
                'logout',
                'transaksi_masuk',
                'transaksi_keluar',
                'cetak_struk',
                'tambah_kendaraan',
                'edit_kendaraan',
                'hapus_kendaraan',
                'tambah_member',
                'edit_member',
                'hapus_member',
                'tambah_tarif',
                'edit_tarif',
                'hapus_tarif',
                'export_laporan',
                'other'
            ])->collation('utf8mb4_0900_ai_ci');
            
            // Deskripsi aktivitas
            $table->text('description')->nullable()
                ->collation('utf8mb4_0900_ai_ci')
                ->comment('Deskripsi detail aktivitas');
            
            // Relasi ke transaksi (jika ada)
            $table->unsignedBigInteger('id_transaksi')->nullable()
                ->comment('FK ke transaksi_parkir jika action terkait transaksi');
            
            // Data tambahan (JSON)
            $table->json('metadata')->nullable()
                ->comment('Data tambahan seperti IP, browser, data sebelum/sesudah edit, dll');
            
            // IP Address
            $table->string('ip_address', 45)->nullable()
                ->collation('utf8mb4_0900_ai_ci');
            
            // User Agent
            $table->string('user_agent', 255)->nullable()
                ->collation('utf8mb4_0900_ai_ci');
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            $table->foreign('id_transaksi')
                ->references('id_transaksi')
                ->on('transaksi_parkir')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            // Indexes untuk performa query
            $table->index('action');
            $table->index('created_at');
            $table->index(['id_user', 'action']);
            $table->index(['created_at', 'action']);
            
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
        Schema::dropIfExists('activity_logs');
    }
};