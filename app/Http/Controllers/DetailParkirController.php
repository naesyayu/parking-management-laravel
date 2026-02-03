<?php

namespace App\Http\Controllers;

use App\Models\DetailParkir;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class DetailParkirController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $details = DetailParkir::orderBy('jam_min')->get();
        return view('detail-parkir.index', compact('details'));
    }

    public function create()
    {
        return view('detail-parkir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jam_min' => 'required|numeric',
            'jam_max' => 'required|numeric|gt:jam_min',
        ]);

        $detail = DetailParkir::create($request->only('jam_min', 'jam_max'));

        $this->logCreate($detail, 'detail_parkir');

        return redirect()->route('detail-parkir.index')
            ->with('success', 'Detail parkir berhasil ditambahkan');
    }

    public function edit(DetailParkir $detailParkir)
    {
        return view('detail-parkir.edit', compact('detailParkir'));
    }

    public function update(Request $request, DetailParkir $detailParkir)
    {
        $request->validate([
            'jam_min' => 'required|numeric',
            'jam_max' => 'required|numeric|gt:jam_min',
        ]);

        $originalData = $detailParkir->toArray();

        $detailParkir->update($request->only('jam_min', 'jam_max'));

        $this->logUpdate($detailParkir, 'detail_parkir', $originalData);

        return redirect()->route('detail-parkir.index')
            ->with('success', 'Detail parkir berhasil diperbarui');
    }

    /**
     * Soft Delete
     */
    public function destroy(DetailParkir $detailParkir)
    {
        $this->logDelete($detailParkir, 'detail_parkir');
        $detailParkir->delete();

        return redirect()->route('detail-parkir.index')
            ->with('success', 'Detail parkir berhasil dihapus');
    }

    /**
     * TRASH
     */
    public function trash()
    {
        $details = DetailParkir::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('detail-parkir.trash', compact('details'));
    }

    /**
     * RESTORE
     */
    public function restore($id)
    {
        $detail = DetailParkir::onlyTrashed()->findOrFail($id);
        $detail->restore();

        $this->logRestore($detail, 'detail_parkir');

        return redirect()->route('detail-parkir.trash')
            ->with('success', 'Detail parkir berhasil dipulihkan');
    }
}
