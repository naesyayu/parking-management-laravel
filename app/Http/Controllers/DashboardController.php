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
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if (!$role) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak ditemukan');
        }

        $data = $this->getDashboardData();

        return view('pages/dashboard.index', compact('data', 'role'));
    }

    private function getDashboardData()
    {
        $today = today();
        
        $stats = [
            'transaksi_masuk_hari_ini' => TransaksiParkir::whereDate('waktu_masuk', $today)->count(),
            'transaksi_keluar_hari_ini' => TransaksiParkir::whereDate('waktu_keluar', $today)->where('status', 'out')->count(),
            'kendaraan_parkir_sekarang' => TransaksiParkir::where('status', 'in')->count(),
            'pendapatan_hari_ini' => TransaksiParkir::whereDate('waktu_keluar', $today)->where('status', 'out')->sum('total_bayar'),
            'diskon_hari_ini' => TransaksiParkir::whereDate('waktu_keluar', $today)->where('status', 'out')->sum('diskon'),
        ];

        $thisMonth = now()->startOfMonth();
        
        $stats['transaksi_bulan_ini'] = TransaksiParkir::whereDate('waktu_keluar', '>=', $thisMonth)->where('status', 'out')->count();
        $stats['pendapatan_bulan_ini'] = TransaksiParkir::whereDate('waktu_keluar', '>=', $thisMonth)->where('status', 'out')->sum('total_bayar');

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
        // OCCUPANCY WITH ALERTS
        // ==========================================
        $occupancy = AreaKapasitas::with(['area', 'tipe'])
            ->get()
            ->map(function($item) {
                $terpakai = DB::table('transaksi_parkir')
                    ->join('kendaraan', 'transaksi_parkir.id_kendaraan', '=', 'kendaraan.id_kendaraan')
                    ->where('transaksi_parkir.id_area', $item->id_area)
                    ->where('kendaraan.id_tipe', $item->id_tipe)
                    ->where('transaksi_parkir.status', 'in')
                    ->count();
                
                $total = $item->kapasitas + $terpakai;
                $persentase = $total > 0 ? round(($terpakai / $total) * 100, 1) : 0;
                
                // Tentukan level alert
                $alertLevel = 'success'; // < 80%
                if ($persentase >= 100) {
                    $alertLevel = 'full'; // 100% = PENUH
                } elseif ($persentase >= 80) {
                    $alertLevel = 'warning'; // 80-99% = HAMPIR PENUH
                }
                
                return [
                    'area' => $item->area->lokasi ?? 'N/A',
                    'tipe' => $item->tipe->tipe_kendaraan ?? 'N/A',
                    'tersedia' => $item->kapasitas,
                    'terpakai' => $terpakai,
                    'total' => $total,
                    'persentase' => $persentase,
                    'alert_level' => $alertLevel,
                ];
            });

        $transaksi_terbaru = TransaksiParkir::with(['kendaraan.tipe', 'areaParkir', 'user'])
            ->where('status', 'in')
            ->orderBy('waktu_masuk', 'desc')
            ->limit(10)
            ->get();

        $chart_labels = [];
        $chart_data_transaksi = [];
        $chart_data_pendapatan = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chart_labels[] = $date->format('d/m');
            
            $transaksi = TransaksiParkir::whereDate('waktu_keluar', $date)->where('status', 'out')->count();
            $pendapatan = TransaksiParkir::whereDate('waktu_keluar', $date)->where('status', 'out')->sum('total_bayar');
            
            $chart_data_transaksi[] = $transaksi;
            $chart_data_pendapatan[] = $pendapatan;
        }

        $activity_logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

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
}