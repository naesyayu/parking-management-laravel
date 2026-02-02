<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiParkir;
use App\Models\ActivityLog;
use App\Models\AreaKapasitas;
use App\Models\Kendaraan;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard utama - redirect atau tampilkan dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if (!$role) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak ditemukan');
        }

        // Data untuk semua role
        $data = $this->getDashboardData();

        return view('dashboard.index', compact('data', 'role'));
    }

    /**
     * Get dashboard data
     */
    private function getDashboardData()
    {
        // ==========================================
        // STATISTIK HARI INI
        // ==========================================
        
        $today = today();
        
        $stats = [
            // Transaksi Hari Ini
            'transaksi_masuk_hari_ini' => TransaksiParkir::whereDate('waktu_masuk', $today)->count(),
            
            'transaksi_keluar_hari_ini' => TransaksiParkir::whereDate('waktu_keluar', $today)
                ->where('status', 'out')
                ->count(),
            
            // Kendaraan Parkir Sekarang
            'kendaraan_parkir_sekarang' => TransaksiParkir::where('status', 'in')->count(),
            
            // Pendapatan Hari Ini
            'pendapatan_hari_ini' => TransaksiParkir::whereDate('waktu_keluar', $today)
                ->where('status', 'out')
                ->sum('total_bayar'),
            
            'diskon_hari_ini' => TransaksiParkir::whereDate('waktu_keluar', $today)
                ->where('status', 'out')
                ->sum('diskon'),
        ];

        // ==========================================
        // STATISTIK BULAN INI
        // ==========================================
        
        $thisMonth = now()->startOfMonth();
        
        $stats['transaksi_bulan_ini'] = TransaksiParkir::whereDate('waktu_keluar', '>=', $thisMonth)
            ->where('status', 'out')
            ->count();
        
        $stats['pendapatan_bulan_ini'] = TransaksiParkir::whereDate('waktu_keluar', '>=', $thisMonth)
            ->where('status', 'out')
            ->sum('total_bayar');

        // ==========================================
        // BREAKDOWN PER TIPE KENDARAAN (Hari Ini)
        // ==========================================
        
        $breakdown_tipe = TransaksiParkir::with('kendaraan.tipe')
            ->whereDate('waktu_keluar', $today)
            ->where('status', 'out')
            ->get()
            ->groupBy(function($item) {
                return $item->kendaraan->tipe->tipe_kendaraan ?? 'Lainnya';
            })
            ->map(function($items, $tipe) {
                return [
                    'tipe' => $tipe,
                    'count' => $items->count(),
                    'pendapatan' => $items->sum('total_bayar'),
                ];
            })
            ->values();

        // ==========================================
        // BREAKDOWN PER METODE PEMBAYARAN (Hari Ini)
        // ==========================================
        
        $breakdown_metode = TransaksiParkir::with('metodePembayaran')
            ->whereDate('waktu_keluar', $today)
            ->where('status', 'out')
            ->get()
            ->groupBy(function($item) {
                return $item->metodePembayaran->metode_bayar ?? 'Tidak Ada';
            })
            ->map(function($items, $metode) {
                return [
                    'metode' => $metode,
                    'count' => $items->count(),
                    'total' => $items->sum('total_bayar'),
                ];
            })
            ->values();

        // ==========================================
        // OCCUPANCY (Kapasitas Parkir)
        // ==========================================
        
        $occupancy = AreaKapasitas::with(['area', 'tipe'])
            ->get()
            ->map(function($item) {
                // Hitung kendaraan yang parkir untuk area & tipe ini
                $terpakai = DB::table('transaksi_parkir')
                    ->join('kendaraan', 'transaksi_parkir.id_kendaraan', '=', 'kendaraan.id_kendaraan')
                    ->where('transaksi_parkir.id_area', $item->id_area)
                    ->where('kendaraan.id_tipe', $item->id_tipe)
                    ->where('transaksi_parkir.status', 'in')
                    ->count();
                
                // Kapasitas total asli (sebelum ada yang parkir)
                $total = $item->kapasitas + $terpakai;
                
                $persentase = $total > 0 ? round(($terpakai / $total) * 100, 1) : 0;
                
                return [
                    'area' => $item->areaParkir->lokasi ?? 'N/A',
                    'tipe' => $item->tipeKendaraan->tipe_kendaraan ?? 'N/A',
                    'tersedia' => $item->kapasitas,
                    'terpakai' => $terpakai,
                    'total' => $total,
                    'persentase' => $persentase,
                ];
            });

        // ==========================================
        // TRANSAKSI TERBARU
        // ==========================================
        
        $transaksi_terbaru = TransaksiParkir::with(['kendaraan.tipe', 'areaParkir', 'user'])
            ->where('status', 'in')
            ->orderBy('waktu_masuk', 'desc')
            ->limit(10)
            ->get();

        // ==========================================
        // CHART DATA (7 Hari Terakhir)
        // ==========================================
        
        $chart_labels = [];
        $chart_data_transaksi = [];
        $chart_data_pendapatan = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chart_labels[] = $date->format('d/m');
            
            $transaksi = TransaksiParkir::whereDate('waktu_keluar', $date)
                ->where('status', 'out')
                ->count();
            
            $pendapatan = TransaksiParkir::whereDate('waktu_keluar', $date)
                ->where('status', 'out')
                ->sum('total_bayar');
            
            $chart_data_transaksi[] = $transaksi;
            $chart_data_pendapatan[] = $pendapatan;
        }

        // ==========================================
        // ACTIVITY LOG TERBARU (Untuk Admin/Owner)
        // ==========================================
        
        $activity_logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ==========================================
        // RETURN ALL DATA
        // ==========================================
        
        return [
            'stats' => $stats,
            'breakdown_tipe' => $breakdown_tipe,
            'breakdown_metode' => $breakdown_metode,
            'occupancy' => $occupancy,
            'transaksi_terbaru' => $transaksi_terbaru,
            'chart_labels' => $chart_labels,
            'chart_data_transaksi' => $chart_data_transaksi,
            'chart_data_pendapatan' => $chart_data_pendapatan,
            'activity_logs' => $activity_logs,
        ];
    }

    // Di DashboardController.php - tambahkan method baru
    public function laporanCustom(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $stats = [
            'total_transaksi' => TransaksiParkir::whereBetween('waktu_keluar', [$startDate, $endDate])
                ->where('status', 'out')
                ->count(),
            
            'total_pendapatan' => TransaksiParkir::whereBetween('waktu_keluar', [$startDate, $endDate])
                ->where('status', 'out')
                ->sum('total_bayar'),
            
            // breakdown per tipe & metode...
        ];

        return view('laporan.custom', compact('stats'));
    }
}