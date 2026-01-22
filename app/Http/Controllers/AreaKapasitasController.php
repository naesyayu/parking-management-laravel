<?php

namespace App\Http\Controllers;

use App\Models\AreaKapasitas;
use App\Models\AreaParkir;
use App\Models\TipeKendaraan;
use Illuminate\Http\Request;

class AreaKapasitasController extends Controller
{
    public function index()
    {
        $data = AreaKapasitas::with(['area', 'tipe'])->get();
        return view('area-kapasitas.index', compact('data'));
    }

    public function create()
    {
        $areas = AreaParkir::all();
        $tipes = TipeKendaraan::all();

        return view('area-kapasitas.create', compact('areas', 'tipes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_area' => 'required|exists:area_parkir,id_area',
            'id_tipe' => 'required|exists:tipe_kendaraan,id_tipe',
            'kapasitas' => 'required|integer|min:0',
        ]);

        AreaKapasitas::create([
            'id_area' => $request->id_area,
            'id_tipe' => $request->id_tipe,
            'kapasitas' => $request->kapasitas,
        ]);

        return redirect()
            ->route('area-kapasitas.index')
            ->with('success', 'Data kapasitas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $area_kapasitas = AreaKapasitas::findOrFail($id);
        $areas = AreaParkir::all();
        $tipes = TipeKendaraan::all();

        return view(
            'area-kapasitas.edit',
            compact('area_kapasitas', 'areas', 'tipes')
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_area' => 'required|exists:area_parkir,id_area',
            'id_tipe' => 'required|exists:tipe_kendaraan,id_tipe',
            'kapasitas' => 'required|integer|min:0',
        ]);

        $area_kapasita = AreaKapasitas::findOrFail($id);

        $area_kapasita->update([
            'id_area' => $request->id_area,
            'id_tipe' => $request->id_tipe,
            'kapasitas' => $request->kapasitas,
        ]);

        return redirect()
            ->route('area-kapasitas.index')
            ->with('success', 'Data kapasitas berhasil diperbarui');
    }

    public function destroy($id)
    {
        $area_kapasita = AreaKapasitas::findOrFail($id);
        $area_kapasita->delete(); // HARD DELETE

        return redirect()
            ->route('area-kapasitas.index')
            ->with('success', 'Data kapasitas berhasil dihapus');
    }
}
