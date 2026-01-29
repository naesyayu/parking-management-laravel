<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Pemilik;
use App\Models\MemberLevel;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with(['pemilik', 'level'])->get();
        return view('member.index', compact('members'));
    }

    public function create()
    {
        $pemiliks = Pemilik::all();
        $levels   = MemberLevel::all();

        return view('member.create', compact('pemiliks', 'levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pemilik'      => 'required|exists:pemilik,id_pemilik',
            'id_level'        => 'required|exists:member_level,id_level',
            'berlaku_mulai'   => 'required|date',
            'berlaku_hingga'  => 'required|date|after_or_equal:berlaku_mulai',
            'status'          => 'required|in:aktif,expired',
        ]);

        Member::create([
            'id_pemilik'     => $request->id_pemilik,
            'id_level'       => $request->id_level,
            'berlaku_mulai'  => $request->berlaku_mulai,
            'berlaku_hingga' => $request->berlaku_hingga,
            'status'         => $request->status,
        ]);

        return redirect()->route('member.index')
            ->with('success', 'Member berhasil ditambahkan');
    }

    public function edit(Member $member)
    {
        $pemiliks = Pemilik::all();
        $levels   = MemberLevel::all();

        return view('member.edit', compact('member', 'pemiliks', 'levels'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'id_pemilik'      => 'required|exists:pemilik,id_pemilik',
            'id_level'        => 'required|exists:member_level,id_level',
            'berlaku_mulai'   => 'required|date',
            'berlaku_hingga'  => 'required|date|after_or_equal:berlaku_mulai',
            'status'          => 'required|in:aktif,expired',
        ]);

        $member->update([
            'id_pemilik'     => $request->id_pemilik,
            'id_level'       => $request->id_level,
            'berlaku_mulai'  => $request->berlaku_mulai,
            'berlaku_hingga' => $request->berlaku_hingga,
            'status'         => $request->status,
        ]);

        return redirect()->route('member.index')
            ->with('success', 'Member berhasil diperbarui');
    }

    public function trash()
    {
        $members = Member::onlyTrashed()
        ->with(['pemilik', 'level'])
        ->get();


        return view('member.trash', compact('members'));
    }

    public function restore($id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);
        $member->restore();


        return redirect()
        ->route('member.trash')
        ->with('success', 'Member berhasil dikembalikan');
    }

    public function destroy(Member $member)
    {
        $member->delete(); // soft delete
        return redirect()->route('member.index')
            ->with('success', 'Member berhasil dihapus');
    }
}
