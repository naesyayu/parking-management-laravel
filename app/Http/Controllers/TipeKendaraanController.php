<?php

namespace App\Http\Controllers;

use App\Models\TipeKendaraan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\ActivityLogger;

class TipeKendaraanController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $tipeKendaraan = TipeKendaraan::orderBy('id_tipe', 'desc')->get();
        return view('tipe-kendaraan.index', compact('tipeKendaraan'));
    }

    public function create()
    {
        return view('tipe-kendaraan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_tipe' => [
                'required',
                Rule::unique('tipe_kendaraan', 'kode_tipe')->whereNull('deleted_at'),
            ],
            'tipe_kendaraan' => [
                'required',
                Rule::unique('tipe_kendaraan', 'tipe_kendaraan')->whereNull('deleted_at'),
            ],
            'deskripsi_tipe' => 'nullable|string',
        ]);

        $tipe = TipeKendaraan::create($request->only([
            'kode_tipe',
            'tipe_kendaraan',
            'deskripsi_tipe'
        ]));

        $this->logCreate($tipe, 'tipe kendaraan');

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
            'kode_tipe' => [
                'required',
                Rule::unique('tipe_kendaraan', 'kode_tipe')
                    ->ignore($tipe_kendaraan->id_tipe, 'id_tipe')
                    ->whereNull('deleted_at'),
            ],
            'tipe_kendaraan' => [
                'required',
                Rule::unique('tipe_kendaraan', 'tipe_kendaraan')
                    ->ignore($tipe_kendaraan->id_tipe, 'id_tipe')
                    ->whereNull('deleted_at'),
            ],
            'deskripsi_tipe' => 'nullable|string',
        ]);

        $original = $tipe_kendaraan->toArray();

        $tipe_kendaraan->update($request->only([
            'kode_tipe',
            'tipe_kendaraan',
            'deskripsi_tipe'
        ]));

        $this->logUpdate($tipe_kendaraan, 'tipe kendaraan', $original);

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

    public function trash()
    {
        $tipeKendaraan = TipeKendaraan::onlyTrashed()->get();
        return view('tipe-kendaraan.trash', compact('tipeKendaraan'));
    }

    public function restore($id)
    {
        $tipe = TipeKendaraan::onlyTrashed()->findOrFail($id);
        $tipe->restore();

        $this->logRestore($tipe, 'tipe kendaraan');

        return redirect()->route('tipe-kendaraan.trash')
            ->with('success', 'Tipe kendaraan berhasil dikembalikan');
    }
}
