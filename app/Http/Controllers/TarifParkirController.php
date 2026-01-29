<?php

namespace App\Http\Controllers;

use App\Models\TarifParkir;
use App\Models\DetailParkir;
use App\Models\TipeKendaraan;
use Illuminate\Http\Request;

class TarifParkirController extends Controller
{
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

        TarifParkir::create($request->all());

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
        $tarifParkir->update($request->all());

        return redirect()
            ->route('tarif-parkir.index')
            ->with('success', 'Tarif parkir berhasil diperbarui');
    }

    // 🔹 DATA TERHAPUS (BACKUP)
    public function trash()
    {
        $tarifParkir = TarifParkir::onlyTrashed()
        ->with(['detailParkir', 'tipeKendaraan'])
        ->orderBy('id_tarif', 'desc')
        ->get();


        return view('tarif-parkir.trash', compact('tarifParkir'));
    }


    // 🔹 RESTORE DATA
    public function restore($id)
    {
        $tarifParkir = TarifParkir::onlyTrashed()->findOrFail($id);
        $tarifParkir->restore();


        return redirect()
        ->route('tarif-parkir.trash')
        ->with('success', 'Tarif parkir berhasil dikembalikan');
    }

    public function destroy($id)
    {
        $tarifParkir = TarifParkir::findOrFail($id);
        $tarifParkir->delete();

        return redirect()
            ->route('tarif-parkir.index')
            ->with('success', 'Tarif parkir berhasil dihapus');
    }
}
