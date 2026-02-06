<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiParkir;
use App\Models\TipeKendaraan;
use App\Models\AreaParkir;
use App\Models\AreaKapasitas;
use App\Models\MetodePembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanHarianController extends Controller
{
    /**
     * Main entry point - mengarahkan berdasarkan role
     */
    public function index(Request $request)
    {
        $role = Auth::user()->role;

        // Petugas langsung ke detail transaksi
        if ($role->isPetugas()) {
            return redirect()->route('laporan.detail-transaksi');
        }

        // Admin dan Owner ke breakdown
        return $this->breakdown($request);
    }

    /**
     * Halaman Breakdown - untuk Admin dan Owner
     * Admin memiliki tombol switch + export
     * Owner hanya melihat breakdown
     */
    public function breakdown(Request $request)
    {
        $role = Auth::user()->role;

        // Filter query transaksi
        $query = TransaksiParkir::with([
            'kendaraan.tipe',
            'kendaraan.pemilik',
            'areaParkir',
            'metodePembayaran'
        ])->where('status', 'out');

        // Apply filters
        if ($request->filled('plat_nomor')) {
            $query->whereHas('kendaraan', fn($q) => 
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%')
            );
        }
        if ($request->filled('id_area')) {
            $query->where('id_area', $request->id_area);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_keluar', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_keluar', '<=', $request->end_date);
        }
        if ($request->filled('id_metode')) {
            $query->where('id_metode', $request->id_metode);
        }
        if ($request->filled('id_tipe')) {
            $query->whereHas('kendaraan', fn($q) => 
                $q->where('id_tipe', $request->id_tipe)
            );
        }

        // Summary statistics
        $summary = [
            'total_transaksi' => (clone $query)->count(),
            'total_pendapatan' => (clone $query)->sum('total_bayar') ?: 0,
            'total_diskon' => (clone $query)->sum('diskon') ?: 0,
            'avg' => (clone $query)->avg('total_bayar') ?: 0,
        ];

        // Breakdown per tipe kendaraan
        $breakdownTipe = (clone $query)->get()
            ->filter(fn($t) => $t->kendaraan)
            ->groupBy('kendaraan.id_tipe')
            ->map(fn($items) => [
                'tipe' => $items->first()->kendaraan->tipe->tipe_kendaraan ?? 'Unknown',
                'jumlah' => $items->count(),
                'revenue' => $items->sum('total_bayar'),
                'avg' => round($items->avg('total_bayar'), 0),
            ]);

        // Breakdown per metode pembayaran
        $breakdownMetode = (clone $query)->get()
            ->groupBy('id_metode')
            ->map(fn($items) => [
                'metode' => $items->first()->metodePembayaran->metode_bayar ?? 'Unknown',
                'jumlah' => $items->count(),
                'revenue' => $items->sum('total_bayar'),
                'avg' => round($items->avg('total_bayar'), 0),
            ]);

        // Occupancy rate per area
        $occupancy = $this->getOccupancy();

        // Data untuk filter dropdown
        $areas = AreaParkir::all();
        $metodes = MetodePembayaran::all();
        $tipes = TipeKendaraan::all();

        return view('pages.laporan.breakdown', compact(
            'summary',
            'breakdownTipe',
            'breakdownMetode',
            'occupancy',
            'areas',
            'metodes',
            'tipes',
            'role'
        ));
    }

    /**
     * Halaman Detail Transaksi - untuk Admin dan Petugas
     * Admin memiliki tombol switch kembali ke breakdown
     * Petugas hanya melihat transaksi mereka sendiri
     */
    public function detailTransaksi(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        // Apply period filter helper
        $this->applyPeriodFilter($request);

        // Base query
        $query = TransaksiParkir::with([
            'kendaraan.tipe',
            'kendaraan.pemilik',
            'areaParkir',
            'metodePembayaran',
            'user',
            'tarifParkir'
        ])->where('status', 'out');

        // Petugas hanya melihat transaksi mereka sendiri
        if ($role->isPetugas()) {
            $query->where('id_user', $user->id_user);
        }

        // Apply filters
        if ($request->filled('plat_nomor')) {
            $query->whereHas('kendaraan', fn($q) => 
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%')
            );
        }
        if ($request->filled('id_tipe')) {
            $query->whereHas('kendaraan', fn($q) => 
                $q->where('id_tipe', $request->id_tipe)
            );
        }
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_keluar', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_keluar', '<=', $request->end_date);
        }

        // Get paginated results
        $transaksi = $query->orderBy('waktu_keluar', 'desc')->paginate(20);
        $tipes = TipeKendaraan::all();

        return view('pages.laporan.detail-transaksi', compact('transaksi', 'tipes', 'role'));
    }

    /**
     * Export CSV - hanya untuk Admin
     */
    public function export(Request $request)
    {
        if (!Auth::user()->role->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Build query with same filters as breakdown
        $query = TransaksiParkir::with([
            'kendaraan.tipe',
            'kendaraan.pemilik',
            'areaParkir',
            'metodePembayaran'
        ])->where('status', 'out');

        // Apply all filters
        if ($request->filled('plat_nomor')) {
            $query->whereHas('kendaraan', fn($q) => 
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%')
            );
        }
        if ($request->filled('id_area')) {
            $query->where('id_area', $request->id_area);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_keluar', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_keluar', '<=', $request->end_date);
        }
        if ($request->filled('id_metode')) {
            $query->where('id_metode', $request->id_metode);
        }
        if ($request->filled('id_tipe')) {
            $query->whereHas('kendaraan', fn($q) => 
                $q->where('id_tipe', $request->id_tipe)
            );
        }

        $transaksi = $query->orderBy('waktu_keluar', 'desc')->get();

        $filename = 'Laporan_Breakdown_' . now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}"
        ];

        $callback = function() use($transaksi) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'Waktu Keluar', 
                'Plat Nomor', 
                'Tipe', 
                'Pemilik', 
                'Area', 
                'Metode', 
                'Durasi (jam)', 
                'Total Bayar', 
                'Diskon'
            ]);
            
            // Data rows
            foreach($transaksi as $t) {
                fputcsv($file, [
                    $t->waktu_keluar->format('d/m/Y H:i'),
                    $t->kendaraan->plat_nomor ?? '-',
                    $t->kendaraan->tipe->tipe_kendaraan ?? '-',
                    $t->kendaraan->pemilik->nama ?? '-',
                    $t->areaParkir->lokasi ?? '-',
                    $t->metodePembayaran->metode_bayar ?? '-',
                    $t->durasi_jam ?? 0,
                    $t->total_bayar,
                    $t->diskon ?? 0
                ]);
            }

            // Summary section
            fputcsv($file, []);
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Transaksi', $transaksi->count()]);
            fputcsv($file, ['Total Pendapatan', $transaksi->sum('total_bayar')]);
            fputcsv($file, ['Total Diskon', $transaksi->sum('diskon')]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper: Apply period filter shortcuts
     */
    private function applyPeriodFilter(Request $request)
    {
        if (!$request->filled('period_type') || $request->period_type === 'custom') {
            return;
        }

        $today = Carbon::now();
        
        switch ($request->period_type) {
            case 'today':
                $request->merge([
                    'start_date' => $today->format('Y-m-d'),
                    'end_date' => $today->format('Y-m-d'),
                ]);
                break;
            case 'week':
                $request->merge([
                    'start_date' => $today->copy()->startOfWeek()->format('Y-m-d'),
                    'end_date' => $today->copy()->endOfWeek()->format('Y-m-d'),
                ]);
                break;
            case 'month':
                $request->merge([
                    'start_date' => $today->copy()->startOfMonth()->format('Y-m-d'),
                    'end_date' => $today->copy()->endOfMonth()->format('Y-m-d'),
                ]);
                break;
        }
    }

    /**
     * Helper: Calculate occupancy rate per area
     */
    private function getOccupancy()
    {
        return AreaKapasitas::with(['area', 'tipe'])
            ->get()
            ->groupBy('id_area')
            ->map(function($items) {
                $firstItem = $items->first();
                
                if (!$firstItem || !$firstItem->area) {
                    return null;
                }
                
                $totalKapasitas = $items->sum('kapasitas');
                $areaId = $firstItem->id_area;
                
                // Count vehicles currently parked in this area
                $totalTerpakai = TransaksiParkir::where('id_area', $areaId)
                    ->where('status', 'in')
                    ->count();
                
                $totalSlot = $totalKapasitas + $totalTerpakai;
                $rate = $totalSlot > 0 
                    ? round(($totalTerpakai / $totalSlot) * 100, 1)
                    : 0;

                return [
                    'area' => $firstItem->area->lokasi,
                    'total' => $totalSlot,
                    'tersedia' => $totalKapasitas,
                    'terpakai' => $totalTerpakai,
                    'rate' => $rate,
                ];
            })
            ->filter();
    }
}