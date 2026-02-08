<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiParkir;
use App\Models\TipeKendaraan;
use App\Models\AreaParkir;
use App\Models\AreaKapasitas;
use App\Models\MetodePembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse; // ← FIX: Import ini

class BreakdownLaporanHarianController extends Controller
{
    public function breakdown(Request $request)
    {
        $role = Auth::user()->role;

        $query = TransaksiParkir::with([
            'kendaraan.tipe',
            'kendaraan.pemilik',
            'areaParkir',
            'metodePembayaran'
        ])->where('status', 'out');

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

        $summary = [
            'total_transaksi' => (clone $query)->count(),
            'total_pendapatan' => (clone $query)->sum('total_bayar') ?: 0,
            'total_diskon' => (clone $query)->sum('diskon') ?: 0,
            'avg' => (clone $query)->avg('total_bayar') ?: 0,
        ];

        $breakdownTipe = (clone $query)->get()
            ->filter(fn($t) => $t->kendaraan)
            ->groupBy('kendaraan.id_tipe')
            ->map(fn($items) => [
                'tipe' => $items->first()->kendaraan->tipe->tipe_kendaraan ?? 'Unknown',
                'jumlah' => $items->count(),
                'revenue' => $items->sum('total_bayar'),
                'avg' => round($items->avg('total_bayar'), 0),
            ]);

        $breakdownMetode = (clone $query)->get()
            ->groupBy('id_metode')
            ->map(fn($items) => [
                'metode' => $items->first()->metodePembayaran->metode_bayar ?? 'Unknown',
                'jumlah' => $items->count(),
                'revenue' => $items->sum('total_bayar'),
                'avg' => round($items->avg('total_bayar'), 0),
            ]);

        $occupancy = $this->getOccupancy();
        $areas = AreaParkir::all();
        $metodes = MetodePembayaran::all();
        $tipes = TipeKendaraan::all();
        $transaksiPerHari = $this->getTransaksiPerHari($query, $request);

        $chartTipe = $breakdownTipe->map(fn($item) => [
            'label' => $item['tipe'],
            'value' => $item['jumlah'],
            'revenue' => $item['revenue']
        ])->values();

        $chartMetode = $breakdownMetode->map(fn($item) => [
            'label' => $item['metode'],
            'value' => $item['jumlah'],
            'revenue' => $item['revenue']
        ])->values();

        return view('pages.laporan.breakdown', compact(
            'summary',
            'breakdownTipe',
            'breakdownMetode',
            'occupancy',
            'areas',
            'metodes',
            'tipes',
            'role',
            'transaksiPerHari',
            'chartTipe',
            'chartMetode'
        ));
    }

    /**
     * EXPORT CSV - FIXED
     */
    public function export(Request $request)
    {
        if (!Auth::user()->role->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat export');
        }

        $query = TransaksiParkir::with([
            'kendaraan.tipe',
            'kendaraan.pemilik',
            'areaParkir',
            'metodePembayaran'
        ])->where('status', 'out');

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

        // FIX: Gunakan StreamedResponse langsung
        return new StreamedResponse(
            function() use ($transaksi) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                
                fputcsv($file, [
                    'Waktu Keluar', 'Plat Nomor', 'Tipe', 'Pemilik', 
                    'Area', 'Metode', 'Durasi (jam)', 'Total', 'Diskon'
                ]);
                
                foreach($transaksi as $t) {
                    fputcsv($file, [
                        $t->waktu_keluar ? $t->waktu_keluar->format('d/m/Y H:i') : '-',
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

                fputcsv($file, []);
                fputcsv($file, ['=== RINGKASAN ===']);
                fputcsv($file, ['Total Transaksi', $transaksi->count()]);
                fputcsv($file, ['Total Pendapatan', $transaksi->sum('total_bayar')]);
                fputcsv($file, ['Total Diskon', $transaksi->sum('diskon')]);
                
                fclose($file);
            },
            200,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]
        );
    }

    private function getTransaksiPerHari($query, $request)
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(29);
        }

        $daysDiff = $startDate->diffInDays($endDate);
        if ($daysDiff > 90) {
            $startDate = $endDate->copy()->subDays(89);
        }

        $data = (clone $query)
            ->select(
                DB::raw('DATE(waktu_keluar) as tanggal'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(total_bayar) as pendapatan')
            )
            ->whereBetween('waktu_keluar', [$startDate, $endDate->endOfDay()])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $period = Carbon::parse($startDate)->daysUntil($endDate);
        $dailyData = collect($period)->map(function($date) use ($data) {
            $dateStr = $date->format('Y-m-d');
            $found = $data->firstWhere('tanggal', $dateStr);
            
            return [
                'tanggal' => $date->format('d M'),
                'tanggal_full' => $dateStr,
                'hari' => $date->locale('id')->dayName,
                'total' => $found ? $found->total : 0,
                'pendapatan' => $found ? $found->pendapatan : 0
            ];
        });

        return $dailyData;
    }

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