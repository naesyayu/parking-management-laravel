<?php

namespace App\Http\Controllers;

use App\Models\Pemilik;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class PemilikController extends Controller
{
    public function index()
    {
        $pemilik = Pemilik::all();
        return view('pemilik.index', compact('pemilik'));
    }

    public function create()
    {
        return view('pemilik.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|max:100',
            'no_hp'  => 'required|max:20',
            'alamat' => 'required',
        ]);

        Pemilik::create($request->all());

        return redirect()->route('pemilik.index')
            ->with('success', 'Pemilik berhasil ditambahkan');
    }

    public function edit(Pemilik $pemilik)
    {
        return view('pemilik.edit', compact('pemilik'));
    }

    public function update(Request $request, Pemilik $pemilik)
    {
        $request->validate([
            'nama'   => 'required|max:100',
            'no_hp'  => 'required|max:20',
            'alamat' => 'required',
        ]);

        $pemilik->update($request->all());

        return redirect()->route('pemilik.index')
            ->with('success', 'Pemilik berhasil diperbarui');
    }

    public function destroy(Pemilik $pemilik)
    {
        $pemilik->delete(); // soft delete

        return redirect()->route('pemilik.index')
            ->with('success', 'Pemilik berhasil dihapus');
    }

    // 🔹 HALAMAN DATA TERHAPUS
    public function trash()
    {
    $pemilik = Pemilik::onlyTrashed()->get();
    return view('pemilik.trash', compact('pemilik'));
    }


    // 🔹 RESTORE DATA
    public function restore($id)
    {
    $pemilik = Pemilik::onlyTrashed()->findOrFail($id);
    $pemilik->restore();


    return redirect()
    ->route('pemilik.trash')
    ->with('success', 'Data berhasil dikembalikan');
    }
}
