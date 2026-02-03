<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pemilik;
use App\Models\TipeKendaraan;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger; // ← IMPORT

class KendaraanController extends Controller
{
    use ActivityLogger; // ← USE TRAIT
    
    public function index()
    {
        $kendaraans = Kendaraan::with(['pemilik', 'tipe'])->get();
        return view('data-kendaraan.index', compact('kendaraans'));
    }

    public function create()
    {
        $pemiliks = Pemilik::all();
        $tipes = TipeKendaraan::all();

        return view('data-kendaraan.create', compact('pemiliks', 'tipes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|unique:kendaraan,plat_nomor|min:10',
            'id_pemilik' => 'nullable|exists:pemilik,id_pemilik',
            'id_tipe' => 'required|exists:tipe_kendaraan,id_tipe',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $kendaraan = Kendaraan::create($request->all());
        
        // ← LOG ACTIVITY
        $this->logCreate($kendaraan, 'kendaraan', [
            'tipe' => $kendaraan->tipe->tipe_kendaraan ?? null,
            'pemilik' => $kendaraan->pemilik->nama ?? 'Tidak ada pemilik',
        ]);

        return redirect()->route('data-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan');
    }

    public function edit(Kendaraan $data_kendaraan)
    {
        $pemiliks = Pemilik::all();
        $tipes = TipeKendaraan::all();

        return view('data-kendaraan.edit', compact('data_kendaraan', 'pemiliks', 'tipes'));
    }

    public function update(Request $request, Kendaraan $data_kendaraan)
    {
        $request->validate([
            'plat_nomor' => 'required|min:9|max:12|unique:kendaraan,plat_nomor,' . $data_kendaraan->id_kendaraan . ',id_kendaraan',
            'id_pemilik' => 'nullable|exists:pemilik,id_pemilik',
            'id_tipe'    => 'required|exists:tipe_kendaraan,id_tipe',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        // Simpan data original untuk track changes
        $originalData = $data_kendaraan->toArray();

        $data_kendaraan->update([
            'plat_nomor' => $request->plat_nomor,
            'id_pemilik' => $request->id_pemilik,
            'id_tipe'    => $request->id_tipe,
            'status'     => $request->status,
        ]);
        
        // Refresh relasi
        $data_kendaraan->load(['pemilik', 'tipe']);
        
        // ← LOG ACTIVITY
        $this->logUpdate($data_kendaraan, 'kendaraan', $originalData, [
            'tipe' => $data_kendaraan->tipe->tipe_kendaraan ?? null,
            'pemilik' => $data_kendaraan->pemilik->nama ?? 'Tidak ada pemilik',
        ]);

        return redirect()
            ->route('data-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil diperbarui');
    }

    public function trash()
    {
        $kendaraans = Kendaraan::onlyTrashed()
        ->with(['pemilik', 'tipe'])
        ->get();

        return view('data-kendaraan.trash', compact('kendaraans'));
    }

    public function restore($id)
    {
        $kendaraan = Kendaraan::onlyTrashed()->findOrFail($id);
        $kendaraan->restore();
        
        // Load relasi
        $kendaraan->load(['pemilik', 'tipe']);
        
        // ← LOG ACTIVITY
        $this->logRestore($kendaraan, 'kendaraan', [
            'tipe' => $kendaraan->tipe->tipe_kendaraan ?? null,
        ]);

        return redirect()
        ->route('data-kendaraan.trash')
        ->with('success', 'Data kendaraan berhasil dikembalikan');
    }

    public function destroy(Kendaraan $data_kendaraan)
    {
        // Load relasi sebelum delete
        $data_kendaraan->load(['pemilik', 'tipe']);
        
        // ← LOG ACTIVITY (SEBELUM DELETE!)
        $this->logDelete($data_kendaraan, 'kendaraan', [
            'tipe' => $data_kendaraan->tipe->tipe_kendaraan ?? null,
            'pemilik' => $data_kendaraan->pemilik->nama ?? 'Tidak ada pemilik',
        ]);
        
        $data_kendaraan->delete(); // soft delete
        
        return redirect()->route('data-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus');
    }
}