<?php

namespace App\Http\Controllers;

use App\Models\TarifParkir;
use App\Models\DetailParkir;
use App\Models\TipeKendaraan;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class TarifParkirController extends Controller
{
    use ActivityLogger;
    
    public function index()
    {
        $tarifParkir = TarifParkir::with(['detailParkir', 'tipeKendaraan'])
            ->orderBy('id_tarif', 'desc')
            ->get();

        return view('tarif-parkir.index', compact('tarifParkir'));
    }

    public function create()
    {
        $detailParkir = DetailParkir::all();
        $tipeKendaraan = TipeKendaraan::all();

        return view('tarif-parkir.create', compact('detailParkir', 'tipeKendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tarif_detail' => 'required|exists:detail_parkir,id_tarif_detail',
            'id_tipe'         => 'required|exists:tipe_kendaraan,id_tipe',
            'tarif'           => 'required|integer|min:0',
        ]);

        $tarifParkir = TarifParkir::create($request->all());
        
        // Load relasi
        $tarifParkir->load(['tipeKendaraan', 'detailParkir']);
        
        $this->logCreate($tarifParkir, 'tarif parkir', [
            'tipe' => $tarifParkir->tipeKendaraan->tipe_kendaraan ?? null,
            'tarif' => 'Rp ' . number_format($tarifParkir->tarif, 0, ',', '.'),
            'range' => $tarifParkir->detailParkir ? 
                $tarifParkir->detailParkir->jam_min . '-' . $tarifParkir->detailParkir->jam_max . ' jam' : null,
        ]);

        return redirect()
            ->route('tarif-parkir.index')
            ->with('success', 'Tarif parkir berhasil ditambahkan');
    }

    public function edit($id)
    {
        $tarifParkir = TarifParkir::findOrFail($id);
        $detailParkir = DetailParkir::all();
        $tipeKendaraan = TipeKendaraan::all();

        return view('tarif-parkir.edit', compact(
            'tarifParkir',
            'detailParkir',
            'tipeKendaraan'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_tarif_detail' => 'required|exists:detail_parkir,id_tarif_detail',
            'id_tipe'         => 'required|exists:tipe_kendaraan,id_tipe',
            'tarif'           => 'required|integer|min:0',
        ]);

        $tarifParkir = TarifParkir::findOrFail($id);
        
        $originalData = $tarifParkir->toArray();
        
        $tarifParkir->update($request->all());
        
        // Refresh relasi
        $tarifParkir->load(['tipeKendaraan', 'detailParkir']);
        
        $this->logUpdate($tarifParkir, 'tarif parkir', $originalData, [
            'tipe' => $tarifParkir->tipeKendaraan->tipe_kendaraan ?? null,
            'tarif_baru' => 'Rp ' . number_format($tarifParkir->tarif, 0, ',', '.'),
        ]);

        return redirect()
            ->route('tarif-parkir.index')
            ->with('success', 'Tarif parkir berhasil diperbarui');
    }

    public function trash()
    {
        $tarifParkir = TarifParkir::onlyTrashed()
        ->with(['detailParkir', 'tipeKendaraan'])
        ->orderBy('id_tarif', 'desc')
        ->get();

        return view('tarif-parkir.trash', compact('tarifParkir'));
    }

    public function restore($id)
    {
        $tarifParkir = TarifParkir::onlyTrashed()->findOrFail($id);
        $tarifParkir->restore();
        
        // Load relasi
        $tarifParkir->load(['tipeKendaraan']);
        
        $this->logRestore($tarifParkir, 'tarif parkir', [
            'tipe' => $tarifParkir->tipeKendaraan->tipe_kendaraan ?? null,
        ]);

        return redirect()
        ->route('tarif-parkir.trash')
        ->with('success', 'Tarif parkir berhasil dikembalikan');
    }

    public function destroy($id)
    {
        $tarifParkir = TarifParkir::findOrFail($id);
        
        // Load relasi sebelum delete
        $tarifParkir->load(['tipeKendaraan']);
        
        $this->logDelete($tarifParkir, 'tarif parkir', [
            'tipe' => $tarifParkir->tipeKendaraan->tipe_kendaraan ?? null,
            'tarif' => 'Rp ' . number_format($tarifParkir->tarif, 0, ',', '.'),
        ]);
        
        $tarifParkir->delete();

        return redirect()
            ->route('tarif-parkir.index')
            ->with('success', 'Tarif parkir berhasil dihapus');
    }
}