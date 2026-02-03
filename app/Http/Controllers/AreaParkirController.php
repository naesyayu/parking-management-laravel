<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ActivityLogger;

class AreaParkirController extends Controller
{
    use ActivityLogger;
    
    public function index()
    {
        $areas = AreaParkir::all();
        return view('area-parkir.index', compact('areas'));
    }

    public function create()
    {
        return view('area-parkir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_area' => 'required|unique:area_parkir,kode_area',
            'lokasi' => 'nullable|string',
            'foto_lokasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_lokasi')) {
            $fotoPath = $request->file('foto_lokasi')
                ->store('area_parkir', 'public');
        }

        $areaParkir = AreaParkir::create([
            'kode_area' => $request->kode_area,
            'lokasi' => $request->lokasi,
            'foto_lokasi' => $fotoPath,
        ]);
        
        $this->logCreate($areaParkir, 'area parkir', [
            'has_foto' => $fotoPath ? 'Ya' : 'Tidak',
        ]);

        return redirect()->route('area-parkir.index')
            ->with('success', 'Area parkir berhasil ditambahkan');
    }

    public function edit(AreaParkir $area_parkir)
    {
        return view('area-parkir.edit', compact('area_parkir'));
    }

    public function update(Request $request, AreaParkir $area_parkir)
    {
        $request->validate([
            'kode_area' => 'required|unique:area_parkir,kode_area,' . $area_parkir->id_area . ',id_area',
            'lokasi' => 'nullable|string',
            'foto_lokasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $originalData = $area_parkir->toArray();
        
        if ($request->hasFile('foto_lokasi')) {
            if ($area_parkir->foto_lokasi) {
                Storage::disk('public')->delete($area_parkir->foto_lokasi);
            }

            $area_parkir->foto_lokasi = $request->file('foto_lokasi')
                ->store('area_parkir', 'public');
        }

        $area_parkir->update([
            'kode_area' => $request->kode_area,
            'lokasi' => $request->lokasi,
        ]);
        
        $this->logUpdate($area_parkir, 'area parkir', $originalData, [
            'foto_updated' => $request->hasFile('foto_lokasi') ? 'Ya' : 'Tidak',
        ]);

        return redirect()->route('area-parkir.index')
            ->with('success', 'Area parkir berhasil diperbarui');
    }

    public function trash()
    {
        $areas = AreaParkir::onlyTrashed()
        ->orderBy('id_area', 'desc')
        ->get();

        return view('area-parkir.trash', compact('areas'));
    }

    public function restore($id)
    {
        $area = AreaParkir::onlyTrashed()->findOrFail($id);
        $area->restore();
        
        $this->logRestore($area, 'area parkir');

        return redirect()
        ->route('area-parkir.trash')
        ->with('success', 'Area parkir berhasil dikembalikan');
    }

    public function destroy(AreaParkir $area_parkir)
    {
        $this->logDelete($area_parkir, 'area parkir', [
            'has_foto' => $area_parkir->foto_lokasi ? 'Ya' : 'Tidak',
        ]);
        
        if ($area_parkir->foto_lokasi) {
            Storage::disk('public')->delete($area_parkir->foto_lokasi);
        }

        $area_parkir->delete();

        return redirect()->route('area-parkir.index')
            ->with('success', 'Area parkir berhasil dihapus');
    }
}