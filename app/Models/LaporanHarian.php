<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanHarian extends Model
{
    protected $table = 'laporan_harian';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'tanggal',
        'total_transaksi',
        'total_kendaraan_masuk',
        'total_kendaraan_keluar',
        'total_pendapatan',
        'total_diskon',
        'pendapatan_bersih',
        'breakdown_tipe',
        'breakdown_metode',
        'breakdown_area',
        'transaksi_member',
        'transaksi_non_member',
        'avg_occupancy_percent',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'breakdown_tipe' => 'array',
        'breakdown_metode' => 'array',
        'breakdown_area' => 'array',
        'total_pendapatan' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'pendapatan_bersih' => 'decimal:2',
        'avg_occupancy_percent' => 'decimal:2',
    ];

    /**
     * =============================================
     * GENERATE LAPORAN UNTUK TANGGAL TERTENTU
     * =============================================
     */
    public static function generate(Carbon $tanggal)
    {
        // Query transaksi yang keluar pada tanggal ini
        $transaksiKeluar = TransaksiParkir::with(['kendaraan.tipe', 'areaParkir', 'metodePembayaran', 'tarifParkir', 'member'])
            ->whereDate('waktu_keluar', $tanggal)
            ->where('status', 'out')
            ->get();

        // Query transaksi masuk
        $transaksiMasuk = TransaksiParkir::whereDate('waktu_masuk', $tanggal)->count();

        // Hitung total
        $totalTransaksi = $transaksiKeluar->count();
        $totalPendapatan = $transaksiKeluar->sum(fn($t) => $t->tarifParkir?->tarif ?? 0);
        $totalDiskon = $transaksiKeluar->sum('diskon');
        $pendapatanBersih = $transaksiKeluar->sum('total_bayar');

        // Breakdown per tipe kendaraan
        $breakdownTipe = $transaksiKeluar->groupBy(fn($t) => $t->kendaraan->tipe->tipe_kendaraan)
            ->map(function ($items, $tipe) {
                return [
                    'count' => $items->count(),
                    'pendapatan' => $items->sum('total_bayar'),
                ];
            })->toArray();

        // Breakdown per metode pembayaran
        $breakdownMetode = $transaksiKeluar->groupBy(fn($t) => $t->metodePembayaran?->metode_bayar ?? 'Tidak Ada')
            ->map(function ($items, $metode) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('total_bayar'),
                ];
            })->toArray();

        // Breakdown per area
        $breakdownArea = $transaksiKeluar->groupBy(fn($t) => $t->areaParkir?->lokasi ?? 'Tidak Ada')
            ->map(function ($items, $area) {
                return [
                    'count' => $items->count(),
                    'pendapatan' => $items->sum('total_bayar'),
                ];
            })->toArray();

        // Member vs Non-member
        $transaksiMember = $transaksiKeluar->filter(fn($t) => $t->id_member !== null)->count();
        $transaksiNonMember = $totalTransaksi - $transaksiMember;

        // Calculate occupancy (opsional - butuh data real-time)
        $avgOccupancy = self::calculateAverageOccupancy($tanggal);

        // Create or update laporan
        return self::updateOrCreate(
            ['tanggal' => $tanggal->toDateString()],
            [
                'total_transaksi' => $totalTransaksi,
                'total_kendaraan_masuk' => $transaksiMasuk,
                'total_kendaraan_keluar' => $totalTransaksi,
                'total_pendapatan' => $totalPendapatan,
                'total_diskon' => $totalDiskon,
                'pendapatan_bersih' => $pendapatanBersih,
                'breakdown_tipe' => $breakdownTipe,
                'breakdown_metode' => $breakdownMetode,
                'breakdown_area' => $breakdownArea,
                'transaksi_member' => $transaksiMember,
                'transaksi_non_member' => $transaksiNonMember,
                'avg_occupancy_percent' => $avgOccupancy,
                'status' => $tanggal->isToday() ? 'draft' : 'final',
            ]
        );
    }

    /**
     * =============================================
     * CALCULATE AVERAGE OCCUPANCY
     * =============================================
     */
    private static function calculateAverageOccupancy(Carbon $tanggal)
    {
        // Implementasi sederhana: hitung dari total kapasitas vs kendaraan parkir
        // Bisa lebih kompleks jika perlu tracking per jam
        
        $totalKapasitas = DB::table('area_kapasitas')->sum('kapasitas');
        
        if ($totalKapasitas == 0) {
            return 0;
        }

        // Hitung rata-rata kendaraan yang parkir pada hari ini
        $avgParked = TransaksiParkir::whereDate('waktu_masuk', '<=', $tanggal)
            ->where(function($q) use ($tanggal) {
                $q->whereNull('waktu_keluar')
                  ->orWhereDate('waktu_keluar', '>=', $tanggal);
            })
            ->count();

        return ($avgParked / $totalKapasitas) * 100;
    }

    /**
     * =============================================
     * SCOPE: BY DATE
     * =============================================
     */
    public function scopeByDate($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    /**
     * =============================================
     * SCOPE: DATE RANGE
     * =============================================
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
}