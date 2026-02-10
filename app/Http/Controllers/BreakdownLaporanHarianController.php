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
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $occupancy = $this->getOccupancyDetailed(); // ← CHANGED METHOD
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
     * ========================================
     * GET OCCUPANCY DETAILED (PER AREA & TIPE)
     * ========================================
     */
    private function getOccupancyDetailed()
    {
        return AreaParkir::with(['kapasitas.tipe'])
            ->get()
            ->map(function($area) {
                $kapasitasDetails = $area->kapasitas->map(function($kap) use ($area) {
                    // Hitung kendaraan yang sedang parkir untuk area & tipe ini
                    $terpakai = TransaksiParkir::where('id_area', $area->id_area)
                        ->where('status', 'in')
                        ->whereHas('kendaraan', function($q) use ($kap) {
                            $q->where('id_tipe', $kap->id_tipe);
                        })
                        ->count();
                    
                    $total = $kap->kapasitas + $terpakai;
                    $rate = $total > 0 
                        ? round(($terpakai / $total) * 100, 1) 
                        : 0;
                    
                    return [
                        'tipe' => $kap->tipe->tipe_kendaraan ?? 'N/A',
                        'kode_tipe' => $kap->tipe->kode_tipe ?? 'N/A',
                        'tersedia' => $kap->kapasitas,
                        'terpakai' => $terpakai,
                        'total' => $total,
                        'rate' => $rate,
                    ];
                });
                
                // Calculate overall occupancy for this area
                $totalKapasitas = $area->kapasitas->sum('kapasitas');
                $totalTerpakai = TransaksiParkir::where('id_area', $area->id_area)
                    ->where('status', 'in')
                    ->count();
                $totalSlot = $totalKapasitas + $totalTerpakai;
                $overallRate = $totalSlot > 0 
                    ? round(($totalTerpakai / $totalSlot) * 100, 1) 
                    : 0;
                
                return [
                    'area_id' => $area->id_area,
                    'area_name' => $area->nama_area,
                    'area_lokasi' => $area->lokasi,
                    'overall_rate' => $overallRate,
                    'total_slot' => $totalSlot,
                    'total_tersedia' => $totalKapasitas,
                    'total_terpakai' => $totalTerpakai,
                    'breakdown' => $kapasitasDetails,
                ];
            })
            ->filter(fn($item) => $item['breakdown']->isNotEmpty()); // Only areas with capacity
    }

    /**
     * ========================================
     * EXPORT EXCEL - PROFESSIONAL FORMAT
     * ========================================
     */
    public function export(Request $request)
    {
        if (!Auth::user()->role->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat export');
        }

        // Build query with same filters
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
        
        // Calculate breakdown data
        $breakdownTipe = $transaksi
            ->filter(fn($t) => $t->kendaraan)
            ->groupBy('kendaraan.id_tipe')
            ->map(fn($items) => [
                'tipe' => $items->first()->kendaraan->tipe->tipe_kendaraan ?? 'Unknown',
                'jumlah' => $items->count(),
                'revenue' => $items->sum('total_bayar'),
                'avg' => round($items->avg('total_bayar'), 0),
            ]);

        $breakdownMetode = $transaksi
            ->groupBy('id_metode')
            ->map(fn($items) => [
                'metode' => $items->first()->metodePembayaran->metode_bayar ?? 'Unknown',
                'jumlah' => $items->count(),
                'revenue' => $items->sum('total_bayar'),
                'avg' => round($items->avg('total_bayar'), 0),
            ]);

        $occupancy = $this->getOccupancyDetailed(); // ← CHANGED METHOD

        $filename = 'Laporan_Breakdown_' . now()->format('Ymd_His') . '.csv';

        return new StreamedResponse(
            function() use ($transaksi, $breakdownTipe, $breakdownMetode, $occupancy, $request) {
                $file = fopen('php://output', 'w');
                
                // UTF-8 BOM untuk Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // ========================================
                // SECTION 1: HEADER LAPORAN
                // ========================================
                fputcsv($file, ['LAPORAN BREAKDOWN TRANSAKSI PARKIR'], ';');
                fputcsv($file, ['Sistem Parkir Management'], ';');
                fputcsv($file, ['Dicetak: ' . now()->format('d/m/Y H:i:s')], ';');
                fputcsv($file, ['User: ' . Auth::user()->username], ';');
                fputcsv($file, [], ';');
                
                // Filter Information
                if ($request->filled('start_date') || $request->filled('end_date')) {
                    $periode = '';
                    if ($request->filled('start_date')) {
                        $periode .= 'Dari: ' . Carbon::parse($request->start_date)->format('d/m/Y');
                    }
                    if ($request->filled('end_date')) {
                        $periode .= ' Sampai: ' . Carbon::parse($request->end_date)->format('d/m/Y');
                    }
                    fputcsv($file, ['Periode: ' . $periode], ';');
                }
                
                fputcsv($file, [], ';');
                fputcsv($file, ['=' . str_repeat('=', 100)], ';');
                fputcsv($file, [], ';');
                
                // ========================================
                // SECTION 2: RINGKASAN UTAMA
                // ========================================
                fputcsv($file, ['RINGKASAN LAPORAN'], ';');
                fputcsv($file, [str_repeat('-', 80)], ';');
                fputcsv($file, [], ';');
                
                fputcsv($file, ['Total Transaksi', $transaksi->count() . ' transaksi'], ';');
                fputcsv($file, ['Total Pendapatan', 'Rp ' . number_format($transaksi->sum('total_bayar'), 0, ',', '.')], ';');
                fputcsv($file, ['Total Diskon', 'Rp ' . number_format($transaksi->sum('diskon'), 0, ',', '.')], ';');
                fputcsv($file, ['Rata-rata Per Transaksi', 'Rp ' . number_format($transaksi->avg('total_bayar'), 0, ',', '.')], ';');
                
                fputcsv($file, [], ';');
                fputcsv($file, ['=' . str_repeat('=', 100)], ';');
                fputcsv($file, [], ';');
                
                // ========================================
                // SECTION 3: BREAKDOWN PER TIPE KENDARAAN
                // ========================================
                fputcsv($file, ['BREAKDOWN PER TIPE KENDARAAN'], ';');
                fputcsv($file, [str_repeat('-', 80)], ';');
                fputcsv($file, [], ';');
                
                fputcsv($file, ['No', 'Tipe Kendaraan', 'Jumlah Transaksi', 'Total Revenue', 'Rata-rata'], ';');
                fputcsv($file, [str_repeat('-', 5), str_repeat('-', 20), str_repeat('-', 20), str_repeat('-', 20), str_repeat('-', 20)], ';');
                
                $no = 1;
                foreach($breakdownTipe as $item) {
                    fputcsv($file, [
                        $no++,
                        $item['tipe'],
                        $item['jumlah'] . ' transaksi',
                        'Rp ' . number_format($item['revenue'], 0, ',', '.'),
                        'Rp ' . number_format($item['avg'], 0, ',', '.')
                    ], ';');
                }
                
                fputcsv($file, [], ';');
                fputcsv($file, ['=' . str_repeat('=', 100)], ';');
                fputcsv($file, [], ';');
                
                // ========================================
                // SECTION 4: BREAKDOWN PER METODE PEMBAYARAN
                // ========================================
                fputcsv($file, ['BREAKDOWN PER METODE PEMBAYARAN'], ';');
                fputcsv($file, [str_repeat('-', 80)], ';');
                fputcsv($file, [], ';');
                
                fputcsv($file, ['No', 'Metode Pembayaran', 'Jumlah Transaksi', 'Total Revenue', 'Rata-rata'], ';');
                fputcsv($file, [str_repeat('-', 5), str_repeat('-', 20), str_repeat('-', 20), str_repeat('-', 20), str_repeat('-', 20)], ';');
                
                $no = 1;
                foreach($breakdownMetode as $item) {
                    fputcsv($file, [
                        $no++,
                        $item['metode'],
                        $item['jumlah'] . ' transaksi',
                        'Rp ' . number_format($item['revenue'], 0, ',', '.'),
                        'Rp ' . number_format($item['avg'], 0, ',', '.')
                    ], ';');
                }
                
                fputcsv($file, [], ';');
                fputcsv($file, ['=' . str_repeat('=', 100)], ';');
                fputcsv($file, [], ';');
                
                // ========================================
                // SECTION 5: OCCUPANCY PER AREA & TIPE
                // ========================================
                fputcsv($file, ['STATUS OCCUPANCY PER AREA & TIPE KENDARAAN'], ';');
                fputcsv($file, [str_repeat('-', 80)], ';');
                fputcsv($file, [], ';');
                
                foreach($occupancy as $area) {
                    fputcsv($file, ['AREA: ' . $area['area_name'] . ' (' . $area['area_lokasi'] . ')'], ';');
                    fputcsv($file, ['Occupancy Rate Keseluruhan: ' . $area['overall_rate'] . '%'], ';');
                    fputcsv($file, ['Total Slot: ' . $area['total_slot'] . ' | Tersedia: ' . $area['total_tersedia'] . ' | Terpakai: ' . $area['total_terpakai']], ';');
                    fputcsv($file, [], ';');
                    
                    fputcsv($file, ['Tipe Kendaraan', 'Total Slot', 'Tersedia', 'Terpakai', 'Occupancy Rate'], ';');
                    fputcsv($file, [str_repeat('-', 20), str_repeat('-', 12), str_repeat('-', 12), str_repeat('-', 12), str_repeat('-', 15)], ';');
                    
                    foreach($area['breakdown'] as $detail) {
                        fputcsv($file, [
                            $detail['tipe'],
                            $detail['total'] . ' slot',
                            $detail['tersedia'] . ' slot',
                            $detail['terpakai'] . ' slot',
                            $detail['rate'] . '%'
                        ], ';');
                    }
                    
                    fputcsv($file, [], ';');
                    fputcsv($file, [str_repeat('-', 80)], ';');
                    fputcsv($file, [], ';');
                }
                
                fputcsv($file, ['=' . str_repeat('=', 100)], ';');
                fputcsv($file, [], ';');
                
                // ========================================
                // SECTION 6: DETAIL TRANSAKSI
                // ========================================
                fputcsv($file, ['DETAIL TRANSAKSI'], ';');
                fputcsv($file, [str_repeat('-', 80)], ';');
                fputcsv($file, [], ';');
                
                fputcsv($file, [
                    'No',
                    'Kode Tiket',
                    'Waktu Masuk',
                    'Waktu Keluar',
                    'Plat Nomor',
                    'Tipe',
                    'Pemilik',
                    'Area',
                    'Metode Bayar',
                    'Durasi (jam)',
                    'Total Bayar',
                    'Diskon',
                    'Net'
                ], ';');
                
                fputcsv($file, [
                    str_repeat('-', 5),
                    str_repeat('-', 18),
                    str_repeat('-', 17),
                    str_repeat('-', 17),
                    str_repeat('-', 12),
                    str_repeat('-', 12),
                    str_repeat('-', 20),
                    str_repeat('-', 20),
                    str_repeat('-', 15),
                    str_repeat('-', 12),
                    str_repeat('-', 15),
                    str_repeat('-', 12),
                    str_repeat('-', 15)
                ], ';');
                
                $no = 1;
                foreach($transaksi as $t) {
                    fputcsv($file, [
                        $no++,
                        $t->kode_tiket ?? '-',
                        $t->waktu_masuk ? $t->waktu_masuk->format('d/m/Y H:i') : '-',
                        $t->waktu_keluar ? $t->waktu_keluar->format('d/m/Y H:i') : '-',
                        $t->kendaraan->plat_nomor ?? '-',
                        $t->kendaraan->tipe->tipe_kendaraan ?? '-',
                        $t->kendaraan->pemilik->nama ?? 'Umum',
                        $t->areaParkir->nama_area ?? '-',
                        $t->metodePembayaran->metode_bayar ?? '-',
                        $t->durasi_jam ?? 0,
                        $t->total_bayar,
                        $t->diskon ?? 0,
                        ($t->total_bayar - ($t->diskon ?? 0))
                    ], ';');
                }
                
                fputcsv($file, [], ';');
                fputcsv($file, [str_repeat('=', 5), str_repeat('=', 100)], ';');
                
                fputcsv($file, [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'SUBTOTAL:',
                    $transaksi->sum('total_bayar'),
                    $transaksi->sum('diskon'),
                    ($transaksi->sum('total_bayar') - $transaksi->sum('diskon'))
                ], ';');
                
                fputcsv($file, [], ';');
                fputcsv($file, ['=' . str_repeat('=', 100)], ';');
                fputcsv($file, [], ';');
                
                // ========================================
                // SECTION 7: FOOTER
                // ========================================
                fputcsv($file, ['CATATAN:'], ';');
                fputcsv($file, ['- Laporan ini dihasilkan secara otomatis oleh sistem'], ';');
                fputcsv($file, ['- Data yang ditampilkan adalah transaksi yang sudah selesai (status: OUT)'], ';');
                fputcsv($file, ['- Occupancy dihitung berdasarkan kendaraan yang sedang parkir (status: IN)'], ';');
                fputcsv($file, ['- Total Bayar sudah termasuk diskon member jika ada'], ';');
                fputcsv($file, ['- Net = Total Bayar - Diskon'], ';');
                fputcsv($file, [], ';');
                fputcsv($file, ['Dicetak oleh: ' . Auth::user()->username . ' pada ' . now()->format('d/m/Y H:i:s')], ';');
                
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

    // OLD METHOD - KEEP FOR BACKWARD COMPATIBILITY IF NEEDED
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
                    'area' => $firstItem->area->nama_area,
                    'total' => $totalSlot,
                    'tersedia' => $totalKapasitas,
                    'terpakai' => $totalTerpakai,
                    'rate' => $rate,
                ];
            })
            ->filter();
    }
}