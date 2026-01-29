<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use Illuminate\Http\Request;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metodePembayaran = MetodePembayaran::all();
        return view('metode-pembayaran.index', compact('metodePembayaran'));
    }

    public function create()
    {
        return view('metode-pembayaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'metode_bayar' => 'required|unique:metode_pembayaran,metode_bayar',
        ]);

        MetodePembayaran::create([
            'metode_bayar' => $request->metode_bayar,
        ]);

        return redirect()->route('metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function edit(MetodePembayaran $metode_pembayaran)
    {
        return view('metode-pembayaran.edit', compact('metode_pembayaran'));
    }

    public function update(Request $request, MetodePembayaran $metode_pembayaran)
    {
        $request->validate([
            'metode_bayar' => 'required|unique:metode_pembayaran,metode_bayar,' 
                . $metode_pembayaran->id_metode . ',id_metode',
        ]);

        $metode_pembayaran->update([
            'metode_bayar' => $request->metode_bayar,
        ]);

        return redirect()->route('metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    // 🔹 BACKUP / TRASH
    public function trash()
    {
        $metodePembayaran = MetodePembayaran::onlyTrashed()
        ->orderBy('id_metode', 'desc')
        ->get();


        return view('metode-pembayaran.trash', compact('metodePembayaran'));
    }


    // 🔹 RESTORE
    public function restore($id)
    {
        $metodePembayaran = MetodePembayaran::onlyTrashed()->findOrFail($id);
        $metodePembayaran->restore();


        return redirect()
        ->route('metode-pembayaran.trash')
        ->with('success', 'Metode pembayaran berhasil dikembalikan');
    }

    public function destroy(MetodePembayaran $metode_pembayaran)
    {
        $metode_pembayaran->delete();

        return redirect()->route('metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil dihapus');
    }

}