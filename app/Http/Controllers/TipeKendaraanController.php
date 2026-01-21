<?php

namespace App\Http\Controllers;

use App\Models\TipeKendaraan;
use Illuminate\Http\Request;

class TipeKendaraanController extends Controller
{
    public function index()
    {
        $tipeKendaraan = TipeKendaraan::all();
        return view('tipe-kendaraan.index', compact('tipeKendaraan'));
    }

    public function create()
    {
        return view('tipe-kendaraan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_kendaraan' => 'required|unique:tipe_kendaraan,tipe_kendaraan',
        ]);

        TipeKendaraan::create([
            'tipe_kendaraan' => $request->tipe_kendaraan,
        ]);

        return redirect()->route('tipe-kendaraan.index')
            ->with('success', 'Tipe kendaraan berhasil ditambahkan');
    }

    public function edit(TipeKendaraan $tipe_kendaraan)
    {
        return view('tipe-kendaraan.edit', compact('tipe_kendaraan'));
    }

    public function update(Request $request, TipeKendaraan $tipe_kendaraan)
    {
        $request->validate([
            'tipe_kendaraan' => 'required|unique:tipe_kendaraan,tipe_kendaraan,' 
                . $tipe_kendaraan->id_tipe . ',id_tipe',
        ]);

        $tipe_kendaraan->update([
            'tipe_kendaraan' => $request->tipe_kendaraan,
        ]);

        return redirect()->route('tipe-kendaraan.index')
            ->with('success', 'Tipe kendaraan berhasil diperbarui');
    }

    public function destroy(TipeKendaraan $tipe_kendaraan)
    {
        $tipe_kendaraan->delete();

        return redirect()->route('tipe-kendaraan.index')
            ->with('success', 'Tipe kendaraan berhasil dihapus');
    }
}
