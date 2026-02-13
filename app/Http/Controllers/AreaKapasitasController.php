<?php

namespace App\Http\Controllers;

use App\Models\AreaKapasitas;
use App\Models\AreaParkir;
use App\Models\TipeKendaraan;
use App\Models\TransaksiParkir;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\DB;

class AreaKapasitasController extends Controller
{
    use ActivityLogger;
    
    /**
     * INDEX - Group by Area
     */
    public function index()
    {
        // Group kapasitas by area
        $areas = AreaParkir::with(['kapasitas.tipe'])->get();
        
        return view('area-kapasitas.index', compact('areas'));
    }

    /**
     * CREATE - Show form to input all vehicle types for one area
     */
    public function create()
    {
        $areas = AreaParkir::all();
        $tipes = TipeKendaraan::all();

        return view('area-kapasitas.create', compact('areas', 'tipes'));
    }

    /**
     * STORE - Save multiple vehicle type capacities for one area
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_area' => 'required|exists:area_parkir,id_area',
            'kapasitas' => 'required|array',
            'kapasitas.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            $area = AreaParkir::findOrFail($request->id_area);
            $inserted = 0;
            
            foreach ($request->kapasitas as $idTipe => $kapasitas) {
                // Skip if capacity is 0
                if ($kapasitas == 0) {
                    continue;
                }
                
                // Check if already exists
                $existing = AreaKapasitas::where('id_area', $request->id_area)
                    ->where('id_tipe', $idTipe)
                    ->first();
                
                if ($existing) {
                    // Update existing
                    $existing->update(['kapasitas' => $kapasitas]);
                } else {
                    // Create new
                    AreaKapasitas::create([
                        'id_area' => $request->id_area,
                        'id_tipe' => $idTipe,
                        'kapasitas' => $kapasitas,
                    ]);
                }
                
                $inserted++;
            }
            
            // Log activity
            $this->logCreate($area, 'area kapasitas', [
                'area' => $area->lokasi,
                'total_tipe' => $inserted,
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('area-kapasitas.index')
                ->with('success', "Kapasitas berhasil disimpan untuk {$inserted} tipe kendaraan");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * EDIT - Edit capacities for specific area
     * ========================================
     * CRITICAL: Check active transactions PER TIPE
     * ========================================
     */
    public function edit($idArea)
    {
        $area = AreaParkir::with('kapasitas')->findOrFail($idArea);
        $tipes = TipeKendaraan::all();
        
        // Create array of existing capacities
        $existingKapasitas = [];
        foreach ($area->kapasitas as $kap) {
            $existingKapasitas[$kap->id_tipe] = $kap->kapasitas;
        }
        
        // ========================================
        // CHECK ACTIVE TRANSACTIONS PER TIPE
        // ========================================
        $activeTransactions = [];
        foreach ($tipes as $tipe) {
            $count = TransaksiParkir::where('id_area', $idArea)
                ->whereHas('kendaraan', function($q) use ($tipe) {
                    $q->where('id_tipe', $tipe->id_tipe);
                })
                ->where('status', 'in') // Masih parkir
                ->count();
            
            $activeTransactions[$tipe->id_tipe] = $count;
        }

        return view('area-kapasitas.edit', compact('area', 'tipes', 'existingKapasitas', 'activeTransactions'));
    }

    /**
     * UPDATE - Update all capacities for an area
     * ========================================
     * CRITICAL: ONLY BLOCK TYPES WITH PARKED VEHICLES
     * ========================================
     */
    public function update(Request $request, $idArea)
    {
        $request->validate([
            'kapasitas' => 'required|array',
            'kapasitas.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            $area = AreaParkir::findOrFail($idArea);
            
            // ========================================
            // CHECK ONLY SUBMITTED TYPES WITH ACTIVE VEHICLES
            // ========================================
            $blockedTipes = [];
            
            foreach ($request->kapasitas as $idTipe => $newKapasitas) {
                // Count active transactions for THIS SPECIFIC TIPE in this area
                $activeCount = TransaksiParkir::where('id_area', $idArea)
                    ->whereHas('kendaraan', function($q) use ($idTipe) {
                        $q->where('id_tipe', $idTipe);
                    })
                    ->where('status', 'in')
                    ->count();
                
                // ========================================
                // HANYA BLOCK JIKA:
                // 1. Ada kendaraan parkir (activeCount > 0)
                // 2. Dan kapasitas baru LEBIH KECIL dari jumlah kendaraan parkir
                // ========================================
                if ($activeCount > 0 && $newKapasitas < $activeCount) {
                    $tipe = TipeKendaraan::find($idTipe);
                    $blockedTipes[] = $tipe->tipe_kendaraan . " (Ada {$activeCount} kendaraan parkir, minimal kapasitas {$activeCount})";
                }
            }
            
            // If any validation fails, BLOCK the update
            if (!empty($blockedTipes)) {
                DB::rollBack();
                
                return back()
                    ->withInput()
                    ->with('error', 'Tidak dapat mengubah kapasitas! ' . implode(', ', $blockedTipes));
            }
            
            // ========================================
            // UPDATE LOGIC - DELETE & RE-INSERT
            // ========================================
            
            // Delete all existing capacities for this area
            AreaKapasitas::where('id_area', $idArea)->delete();
            
            // Insert new capacities
            $inserted = 0;
            foreach ($request->kapasitas as $idTipe => $kapasitas) {
                if ($kapasitas > 0) {
                    AreaKapasitas::create([
                        'id_area' => $idArea,
                        'id_tipe' => $idTipe,
                        'kapasitas' => $kapasitas,
                    ]);
                    $inserted++;
                }
            }
            
            // Log activity
            $this->logUpdate($area, 'area kapasitas', [], [
                'area' => $area->lokasi,
                'total_tipe' => $inserted,
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('area-kapasitas.index')
                ->with('success', 'Kapasitas berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY - Delete all capacities for an area
     * ========================================
     * CRITICAL: Prevent delete if ANY vehicles are parked
     * ========================================
     */
    public function destroy($idArea)
    {
        DB::beginTransaction();
        
        try {
            $area = AreaParkir::findOrFail($idArea);
            
            // ========================================
            // CHECK ACTIVE TRANSACTIONS BEFORE DELETE
            // ========================================
            $activeCount = TransaksiParkir::where('id_area', $idArea)
                ->where('status', 'in')
                ->count();
            
            if ($activeCount > 0) {
                DB::rollBack();
                
                return back()->with('error', "Tidak dapat menghapus kapasitas! Masih ada {$activeCount} kendaraan yang sedang parkir di area ini.");
            }
            
            $count = $area->kapasitas()->count();
            
            $area->kapasitas()->delete();
            
            $this->logDelete($area, 'area kapasitas', [
                'area' => $area->lokasi,
                'deleted_count' => $count,
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('area-kapasitas.index')
                ->with('success', "Semua kapasitas area ({$count} tipe) berhasil dihapus");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}