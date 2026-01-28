<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pemilik;
use App\Models\TipeKendaraan;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
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

        Kendaraan::create($request->all());

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

        $data_kendaraan->update([
            'plat_nomor' => $request->plat_nomor,
            'id_pemilik' => $request->id_pemilik,
            'id_tipe'    => $request->id_tipe,
            'status'     => $request->status,
        ]);

        return redirect()
            ->route('data-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil diperbarui');
    }


    public function destroy(Kendaraan $data_kendaraan)
    {
        $data_kendaraan->delete(); // soft delete
        return redirect()->route('data-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus');
    }
}
