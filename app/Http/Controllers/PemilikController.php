<?php

namespace App\Http\Controllers;

use App\Models\Pemilik;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class PemilikController extends Controller
{
    use ActivityLogger;
    
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

        $pemilik = Pemilik::create($request->all());
        
        $this->logCreate($pemilik, 'pemilik');

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

        $originalData = $pemilik->toArray();
        
        $pemilik->update($request->all());
        
        $this->logUpdate($pemilik, 'pemilik', $originalData);

        return redirect()->route('pemilik.index')
            ->with('success', 'Pemilik berhasil diperbarui');
    }

    public function destroy(Pemilik $pemilik)
    {
        $this->logDelete($pemilik, 'pemilik');
        
        $pemilik->delete(); // soft delete

        return redirect()->route('pemilik.index')
            ->with('success', 'Pemilik berhasil dihapus');
    }

    public function trash()
    {
        $pemilik = Pemilik::onlyTrashed()->get();
        return view('pemilik.trash', compact('pemilik'));
    }

    public function restore($id)
    {
        $pemilik = Pemilik::onlyTrashed()->findOrFail($id);
        $pemilik->restore();
        
        $this->logRestore($pemilik, 'pemilik');

        return redirect()
        ->route('pemilik.trash')
        ->with('success', 'Data berhasil dikembalikan');
    }
}