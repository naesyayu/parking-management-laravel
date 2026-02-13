<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreaParkir;
use App\Models\AreaKapasitas;
use App\Models\TransaksiParkir;

class LobbyDisplayController extends Controller
{
    /**
     * Display real-time parking occupancy for lobby display
     * Accessible by ALL roles
     */
    public function index()
    {
        $occupancyData = $this->getRealTimeOccupancy();
        
        return view('pages.lobby.display', [
            'areas' => $occupancyData
        ]);
    }

    /**
     * API endpoint for real-time updates (AJAX polling)
     */
    public function getOccupancyData()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getRealTimeOccupancy(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get real-time occupancy data per area & tipe
     */
    private function getRealTimeOccupancy()
    {
        return AreaParkir::with(['kapasitas.tipe'])
            ->get()
            ->map(function($area) {
                $breakdown = $area->kapasitas->map(function($kap) use ($area) {
                    // Count vehicles currently parked (status: IN)
                    $terpakai = TransaksiParkir::where('id_area', $area->id_area)
                        ->where('status', 'in')
                        ->whereHas('kendaraan', function($q) use ($kap) {
                            $q->where('id_tipe', $kap->id_tipe);
                        })
                        ->count();
                    
                    $tersedia = $kap->kapasitas;
                    $total = $tersedia + $terpakai;
                    $rate = $total > 0 ? round(($terpakai / $total) * 100, 1) : 0;
                    
                    // Determine status color
                    // GREEN: 0-79%
                    // YELLOW: 80-99%
                    // RED: 100%
                    $status = 'available'; // green
                    if ($rate >= 100) {
                        $status = 'full'; // red
                    } elseif ($rate >= 80) {
                        $status = 'almost-full'; // yellow
                    }
                    
                    return [
                        'id_tipe' => $kap->id_tipe,
                        'tipe' => $kap->tipe->tipe_kendaraan ?? 'N/A',
                        'kode_tipe' => $kap->tipe->kode_tipe ?? 'N/A',
                        'tersedia' => $tersedia,
                        'terpakai' => $terpakai,
                        'total' => $total,
                        'rate' => $rate,
                        'status' => $status,
                    ];
                });
                
                // Calculate overall area occupancy
                $totalKapasitas = $area->kapasitas->sum('kapasitas');
                $totalTerpakai = TransaksiParkir::where('id_area', $area->id_area)
                    ->where('status', 'in')
                    ->count();
                $totalSlot = $totalKapasitas + $totalTerpakai;
                $overallRate = $totalSlot > 0 ? round(($totalTerpakai / $totalSlot) * 100, 1) : 0;
                
                // Overall status
                $overallStatus = 'available';
                if ($overallRate >= 100) {
                    $overallStatus = 'full';
                } elseif ($overallRate >= 80) {
                    $overallStatus = 'almost-full';
                }
                
                return [
                    'id_area' => $area->id_area,
                    'area_name' => $area->nama_area,
                    'area_lokasi' => $area->lokasi,
                    'overall_rate' => $overallRate,
                    'overall_status' => $overallStatus,
                    'total_slot' => $totalSlot,
                    'total_tersedia' => $totalKapasitas,
                    'total_terpakai' => $totalTerpakai,
                    'breakdown' => $breakdown,
                ];
            })
            ->filter(fn($item) => $item['breakdown']->isNotEmpty())
            ->values();
    }
}