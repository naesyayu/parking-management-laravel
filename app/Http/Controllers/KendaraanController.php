<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanHarian;
use App\Models\TransaksiParkir;
use App\Models\TipeKendaraan;
use App\Models\AreaParkir;
use App\Models\AreaKapasitas;
use App\Models\MetodePembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LaporanHarianController extends Controller
{
    /**
     * Index - Auto-detect role & render view sesuai akses
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        if (!$role) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak ditemukan');
        }

        // OWNER: Full dashboard dengan semua fitur
        if ($role->isOwner()) {
            return $this->ownerView($request);
        }

        // ADMIN: Filter tanggal + agregat
        if ($role->isAdmin()) {
            return $this->adminView($request);
        }

        // PETUGAS: Search by plat/tanggal saja
        if ($role->isPetugas()) {
            return $this->petugasView($request);
        }

        // Jika role tidak dikenali
        abort(403, 'Anda tidak memiliki akses ke halaman ini');
    }

    /**
     * OWNER VIEW
     * Fitur: Generate laporan hari ini, filter tanggal, breakdown tipe, occupancy, export
     */
    private function ownerView(Request $request)
    {
        // 1. Generate laporan hari ini
        $today = Carbon::today();
        LaporanHarian::generateForDate($today);
        $laporanHariIni = LaporanHarian::where('tanggal', $today->format('Y-m-d'))->first();

        // 2. Filter tanggal range
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)
            : Carbon::today()->subDays(6);
        
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::today();

        $idTipe = $request->get('id_tipe', 'all');

        // Auto-generate untuk range
        $this->autoGenerateLaporan($startDate, $endDate);

        // 3. Laporan per hari (tabel)
        $laporanRange = LaporanHarian::dateRange(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        )->orderBy('tanggal', 'desc')->paginate(10);

        // 4. Breakdown per tipe kendaraan
        $breakdownTipe = $this->getBreakdownTipe($startDate, $endDate, $idTipe);

        // 5. Breakdown per metode pembayaran
        $breakdownMetode = $this->getBreakdownMetode($startDate, $endDate, $idTipe);

        // 6. Occupancy/Kapasitas
        $occupancy = $this->getOccupancy();

        // 7. Tipe kendaraan untuk filter
        $tipeKendaraan = TipeKendaraan::all();

        return view('laporan.index', compact(
            'laporanHariIni',
            'laporanRange',
            'breakdownTipe',
            'breakdownMetode',
            'occupancy',
            'tipeKendaraan',
            'startDate',
            'endDate',
            'idTipe'
        ));
    }

    /**
     * ADMIN VIEW
     * Fitur: Filter tanggal mulai-akhir, agregat data, view detail transaksi
     */
    private function adminView(Request $request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)
            : Carbon::today()->subDays(7);
        
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::today();

        // Auto-generate
        $this->autoGenerateLaporan($startDate, $endDate);

        // Agregat data untuk range tersebut
        $agregat = $this->getAgregatData($startDate, $endDate);

        // Laporan per hari
        $laporan = LaporanHarian::dateRange(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        )->orderBy('tanggal', 'desc')->paginate(15);

        return view('laporan.index', compact(
            'laporan',
            'agregat',
            'startDate',
            'endDate'
        ));
    }

    /**
     * PETUGAS VIEW
     * Fitur: Cari transaksi by plat atau tanggal
     */
    private function petugasView(Request $request)
    {
        $query = TransaksiParkir::with([
            'kendaraan.tipe',
            'kendaraan.pemilik',
            'areaParkir',
            'metodePembayaran',
            'user'
        ])->where('status', 'out');

        // Filter plat
        if ($request->filled('plat_nomor')) {
            $query->whereHas('kendaraan', function($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%');
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('waktu_keluar', $request->tanggal);
        }

        $transaksi = $query->orderBy('waktu_keluar', 'desc')->paginate(50);

        return view('laporan.index', compact('transaksi'));
    }

    /**
     * SHOW DETAIL TRANSAKSI (All roles)
     */
    public function show($id)
    {
        $transaksi = TransaksiParkir::with([
            'kendaraan.tipe',
            'kendaraan.pemilik',
            'areaParkir',
            'metodePembayaran',
            'user',
            'member'
        ])->findOrFail($id);

        return view('laporan.show', compact('transaksi'));
    }

    /**
     * EXPORT CSV (Owner & Admin only)
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!$user->role->isOwner() && !$user->role->isAdmin()) {
            abort(403, 'Akses ditolak - Hanya Owner dan Admin yang dapat export laporan');
        }

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)
            : Carbon::today()->subDays(6);
        
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::today();

        $idTipe = $request->get('id_tipe', 'all');

        // Generate laporan
        $this->autoGenerateLaporan($startDate, $endDate);

        // Get data berdasarkan filter
        $laporan = LaporanHarian::dateRange(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        )->orderBy('tanggal', 'asc')->get();

        $filename = 'laporan-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($laporan) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, [
                'Tanggal',
                'Total Transaksi',
                'Kendaraan Masuk',
                'Kendaraan Keluar',
                'Total Pendapatan',
                'Total Diskon',
                'Pendapatan Bersih',
                'Member',
                'Non-Member',
            ]);

            foreach ($laporan as $item) {
                fputcsv($file, [
                    $item->tanggal->format('d/m/Y'),
                    $item->total_transaksi,
                    $item->total_kendaraan_masuk,
                    $item->total_kendaraan_keluar,
                    $item->total_pendapatan,
                    $item->total_diskon,
                    $item->pendapatan_bersih,
                    $item->transaksi_member,
                    $item->transaksi_non_member,
                ]);
            }

            fclose($file);
        };

        \App\Models\ActivityLog::log(
            'export_laporan',
            'Export laporan CSV: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y')
        );

        return response()->stream($callback, 200, $headers);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    private function autoGenerateLaporan($startDate, $endDate)
    {
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            LaporanHarian::generateForDate($currentDate);
            $currentDate->addDay();
        }
    }

    private function getBreakdownTipe($startDate, $endDate, $idTipe)
    {
        $query = TransaksiParkir::with('kendaraan.tipe')
            ->whereBetween('waktu_keluar', [$startDate, $endDate])
            ->where('status', 'out');

        if ($idTipe !== 'all') {
            $query->whereHas('kendaraan', function($q) use ($idTipe) {
                $q->where('id_tipe', $idTipe);
            });
        }

        return $query->get()
            ->groupBy(function($item) {
                return $item->kendaraan->tipe->tipe_kendaraan ?? 'Lainnya';
            })
            ->map(function($items) {
                return [
                    'count' => $items->count(),
                    'revenue' => $items->sum('total_bayar'),
                ];
            });
    }

    private function getBreakdownMetode($startDate, $endDate, $idTipe)
    {
        $query = TransaksiParkir::with('metodePembayaran')
            ->whereBetween('waktu_keluar', [$startDate, $endDate])
            ->where('status', 'out');

        if ($idTipe !== 'all') {
            $query->whereHas('kendaraan', function($q) use ($idTipe) {
                $q->where('id_tipe', $idTipe);
            });
        }

        return $query->get()
            ->groupBy(function($item) {
                return $item->metodePembayaran->metode_bayar ?? 'Tidak Ada';
            })
            ->map(function($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('total_bayar'),
                ];
            });
    }

    private function getOccupancy()
    {
        return AreaKapasitas::with(['areaParkir', 'tipeKendaraan'])
            ->get()
            ->map(function($item) {
                $terpakai = DB::table('transaksi_parkir')
                    ->join('kendaraan', 'transaksi_parkir.id_kendaraan', '=', 'kendaraan.id_kendaraan')
                    ->where('transaksi_parkir.id_area', $item->id_area)
                    ->where('kendaraan.id_tipe', $item->id_tipe)
                    ->where('transaksi_parkir.status', 'in')
                    ->count();
                
                $total = $item->kapasitas;
                $available = $total - $terpakai;
                $persentase = $total > 0 ? round(($terpakai / $total) * 100, 1) : 0;
                
                return [
                    'area' => $item->areaParkir->lokasi ?? 'N/A',
                    'tipe' => $item->tipeKendaraan->tipe_kendaraan ?? 'N/A',
                    'total' => $total,
                    'terpakai' => $terpakai,
                    'available' => $available,
                    'persentase' => $persentase,
                ];
            });
    }

    private function getAgregatData($startDate, $endDate)
    {
        $transaksi = TransaksiParkir::whereBetween('waktu_keluar', [$startDate, $endDate])
            ->where('status', 'out')
            ->get();

        return [
            'total_transaksi' => $transaksi->count(),
            'total_pendapatan' => $transaksi->sum('total_bayar') + $transaksi->sum('diskon'),
            'total_diskon' => $transaksi->sum('diskon'),
            'pendapatan_bersih' => $transaksi->sum('total_bayar'),
            'avg_transaction' => $transaksi->count() > 0 ? $transaksi->sum('total_bayar') / $transaksi->count() : 0,
        ];
    }
}