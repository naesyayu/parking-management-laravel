<?php

namespace App\Http\Controllers;

use App\Models\TipeKendaraan;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class TipeKendaraanController extends Controller
{
    use ActivityLogger;
    
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

        $tipeKendaraan = TipeKendaraan::create($request->all());
        
        $this->logCreate($tipeKendaraan, 'tipe kendaraan');

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

        $originalData = $tipe_kendaraan->toArray();
        
        $tipe_kendaraan->update([
            'kode_tipe' => $request->kode_tipe,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'deskripsi_tipe' => $request->deskripsi_tipe,
        ]);
        
        $this->logUpdate($tipe_kendaraan, 'tipe kendaraan', $originalData);

        return redirect()->route('tipe-kendaraan.index')
            ->with('success', 'Tipe kendaraan berhasil diperbarui');
    }

    public function destroy(TipeKendaraan $tipe_kendaraan)
    {
        $this->logDelete($tipe_kendaraan, 'tipe kendaraan');
        
        $tipe_kendaraan->delete();

        return redirect()->route('tipe-kendaraan.index')
            ->with('success', 'Tipe kendaraan berhasil dihapus');
    }
}