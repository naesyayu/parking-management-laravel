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
            'kode_tipe' => 'required|unique:tipe_kendaraan,kode_tipe',
            'tipe_kendaraan' => 'required|unique:tipe_kendaraan,tipe_kendaraan',
            'deskripsi_tipe' => 'nullable|string',
        ]);

        TipeKendaraan::create([
            'kode_tipe' => 'required|unique:tipe_kendaraan,kode_tipe',
            'tipe_kendaraan' => 'required|unique:tipe_kendaraan,tipe_kendaraan',
            'deskripsi_tipe' => 'nullable|string',
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
            'kode_tipe' => 'required|unique:tipe_kendaraan,kode_tipe,'
            . $tipe_kendaraan->id_tipe . ',id_tipe',


            'tipe_kendaraan' => 'required|unique:tipe_kendaraan,tipe_kendaraan,'
            . $tipe_kendaraan->id_tipe . ',id_tipe',


            'deskripsi_tipe' => 'nullable|string',
        ]);

        $tipe_kendaraan->update([
            'kode_tipe' => $request->kode_tipe,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'deskripsi_tipe' => $request->deskripsi_tipe,
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
