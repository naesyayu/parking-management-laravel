<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pemilik;
use App\Models\TipeKendaraan;
use App\Rules\PlatNomorIndonesia;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with(['pemilik', 'tipe']);
        
        // SEARCH by plat_nomor
        if ($request->filled('search')) {
            $query->where('plat_nomor', 'like', '%' . $request->search . '%');
        }
        
        $kendaraans = $query->get();
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
            'plat_nomor' => ['required', new PlatNomorIndonesia, 'unique:kendaraan,plat_nomor'],
            'id_pemilik' => 'nullable|exists:pemilik,id_pemilik',
            'id_tipe' => 'required|exists:tipe_kendaraan,id_tipe',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Normalize plat nomor before saving
        $platNomor = PlatNomorIndonesia::normalize($request->plat_nomor);

        Kendaraan::create([
            'plat_nomor' => $platNomor,
            'id_pemilik' => $request->id_pemilik,
            'id_tipe' => $request->id_tipe,
            'status' => $request->status,
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
            'plat_nomor' => [
                'required', 
                new PlatNomorIndonesia, 
                'unique:kendaraan,plat_nomor,' . $data_kendaraan->id_kendaraan . ',id_kendaraan'
            ],
            'id_pemilik' => 'nullable|exists:pemilik,id_pemilik',
            'id_tipe'    => 'required|exists:tipe_kendaraan,id_tipe',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        // Normalize plat nomor before updating
        $platNomor = PlatNomorIndonesia::normalize($request->plat_nomor);

        $data_kendaraan->update([
            'plat_nomor' => $platNomor,
            'id_pemilik' => $request->id_pemilik,
            'id_tipe'    => $request->id_tipe,
            'status'     => $request->status,
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

        return redirect()
            ->route('data-kendaraan.trash')
            ->with('success', 'Data kendaraan berhasil dikembalikan');
    }

    public function destroy(Kendaraan $data_kendaraan)
    {
        $data_kendaraan->delete();
        return redirect()->route('data-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus');
    }
}