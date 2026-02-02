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
        
        Schema::create('laporan_harian', function (Blueprint $table) {
            $table->id('id_laporan');
            
            // Tanggal laporan
            $table->date('tanggal')
                ->unique()
                ->comment('Tanggal laporan (unique per hari)');
            
            // Total transaksi
            $table->integer('total_transaksi')->default(0)
                ->comment('Total transaksi keluar pada hari ini');
            
            $table->integer('total_kendaraan_masuk')->default(0)
                ->comment('Total kendaraan yang masuk');
            
            $table->integer('total_kendaraan_keluar')->default(0)
                ->comment('Total kendaraan yang keluar');
            
            // Pendapatan
            $table->decimal('total_pendapatan', 15, 2)->default(0)
                ->comment('Total pendapatan kotor (sebelum diskon)');
            
            $table->decimal('total_diskon', 15, 2)->default(0)
                ->comment('Total diskon yang diberikan');
            
            $table->decimal('pendapatan_bersih', 15, 2)->default(0)
                ->comment('Total pendapatan bersih (setelah diskon)');
            
            // Breakdown per tipe kendaraan (JSON)
            $table->json('breakdown_tipe')->nullable()
                ->comment('{"Motor": {"count": 50, "pendapatan": 150000}, "Mobil": {...}}');
            
            // Breakdown per metode pembayaran (JSON)
            $table->json('breakdown_metode')->nullable()
                ->comment('{"Tunai": {"count": 30, "total": 90000}, "QRIS": {...}}');
            
            // Breakdown per area parkir (JSON)
            $table->json('breakdown_area')->nullable()
                ->comment('{"Area A": {"count": 20, "pendapatan": 60000}, ...}');
            
            // Member statistics
            $table->integer('transaksi_member')->default(0)
                ->comment('Jumlah transaksi oleh member');
            
            $table->integer('transaksi_non_member')->default(0)
                ->comment('Jumlah transaksi non-member');
            
            // Occupancy rata-rata (opsional)
            $table->decimal('avg_occupancy_percent', 5, 2)->nullable()
                ->comment('Rata-rata persentase occupancy hari ini');
            
            // Status generate
            $table->enum('status', ['draft', 'final'])
                ->default('draft')
                ->collation('utf8mb4_0900_ai_ci')
                ->comment('draft = sedang berjalan, final = hari sudah selesai');
            
            $table->timestamps();
            
            // Indexes
            $table->index('tanggal');
            $table->index(['tanggal', 'status']);
            
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
        Schema::dropIfExists('laporan_harian');
    }
};