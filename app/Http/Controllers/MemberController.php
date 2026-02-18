<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Pemilik;
use App\Models\MemberLevel;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger; 

class MemberController extends Controller
{
    use ActivityLogger; 
    
    public function index(Request $request)
    {
        $query = Member::with(['pemilik', 'level']);
        
        // SEARCH by nama pemilik
        if ($request->filled('search')) {
            $query->whereHas('pemilik', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }
        
        $members = $query->get();
        return view('member.index', compact('members'));
    }

    public function create()
    {
        // Ambil ID pemilik yang sudah terdaftar sebagai member (tidak termasuk yang soft deleted)
        $registeredPemilikIds = Member::pluck('id_pemilik')->toArray();
        
        // Tampilkan hanya pemilik yang belum terdaftar sebagai member
        $pemiliks = Pemilik::whereNotIn('id_pemilik', $registeredPemilikIds)->get();
        
        $levels = MemberLevel::all();

        return view('member.create', compact('pemiliks', 'levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pemilik'      => [
                'required',
                'exists:pemilik,id_pemilik',
                // Validasi: pastikan pemilik belum terdaftar sebagai member
                function ($attribute, $value, $fail) {
                    if (Member::where('id_pemilik', $value)->exists()) {
                        $pemilik = Pemilik::find($value);
                        $fail('Pemilik "' . ($pemilik->nama ?? 'ini') . '" sudah terdaftar sebagai member.');
                    }
                },
            ],
            'id_level'        => 'required|exists:member_level,id_level',
            'berlaku_mulai'   => 'required|date|after_or_equal:today',
            'berlaku_hingga'  => 'required|date|after:berlaku_mulai',
            'status'          => 'required|in:aktif,expired',
        ], [
            'id_pemilik.required' => 'Pemilik harus dipilih',
            'id_pemilik.exists' => 'Pemilik tidak valid',
            'id_level.required' => 'Level member harus dipilih',
            'berlaku_mulai.required' => 'Tanggal berlaku mulai harus diisi',
            'berlaku_mulai.after_or_equal' => 'Tanggal berlaku mulai tidak boleh sebelum hari ini',
            'berlaku_hingga.required' => 'Tanggal berlaku hingga harus diisi',
            'berlaku_hingga.after' => 'Tanggal berlaku hingga harus setelah tanggal berlaku mulai',
            'status.required' => 'Status harus dipilih',
        ]);

        $member = Member::create([
            'id_pemilik'     => $request->id_pemilik,
            'id_level'       => $request->id_level,
            'berlaku_mulai'  => $request->berlaku_mulai,
            'berlaku_hingga' => $request->berlaku_hingga,
            'status'         => $request->status,
        ]);
        
        // Load relasi
        $member->load(['pemilik', 'level']);
        
        // ← LOG ACTIVITY
        $this->logCreate($member, 'member', [
            'pemilik' => $member->pemilik->nama ?? null,
            'level' => $member->level->nama_level ?? null,
            'berlaku_hingga' => $member->berlaku_hingga,
        ]);

        return redirect()->route('member.index')
            ->with('success', 'Member berhasil ditambahkan');
    }

    public function edit(Member $member)
    {
        // Ambil ID pemilik yang sudah terdaftar sebagai member
        // KECUALI pemilik dari member yang sedang diedit
        $registeredPemilikIds = Member::where('id_member', '!=', $member->id_member)
                                      ->pluck('id_pemilik')
                                      ->toArray();
        
        // Tampilkan pemilik yang belum terdaftar + pemilik member saat ini
        $pemiliks = Pemilik::whereNotIn('id_pemilik', $registeredPemilikIds)->get();
        
        $levels = MemberLevel::all();

        return view('member.edit', compact('member', 'pemiliks', 'levels'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'id_pemilik'      => [
                'required',
                'exists:pemilik,id_pemilik',
                // Validasi: pastikan pemilik belum terdaftar sebagai member (kecuali member ini sendiri)
                function ($attribute, $value, $fail) use ($member) {
                    $exists = Member::where('id_pemilik', $value)
                                    ->where('id_member', '!=', $member->id_member)
                                    ->exists();
                    
                    if ($exists) {
                        $pemilik = Pemilik::find($value);
                        $fail('Pemilik "' . ($pemilik->nama ?? 'ini') . '" sudah terdaftar sebagai member lain.');
                    }
                },
            ],
            'id_level'        => 'required|exists:member_level,id_level',
            'berlaku_mulai'   => 'required|date|after_or_equal:today',
            'berlaku_hingga'  => 'required|date|after:berlaku_mulai',
            'status'          => 'required|in:aktif,expired',
        ], [
            'id_pemilik.required' => 'Pemilik harus dipilih',
            'id_pemilik.exists' => 'Pemilik tidak valid',
            'id_level.required' => 'Level member harus dipilih',
            'berlaku_mulai.required' => 'Tanggal berlaku mulai harus diisi',
            'berlaku_mulai.after_or_equal' => 'Tanggal berlaku mulai tidak boleh sebelum hari ini',
            'berlaku_hingga.required' => 'Tanggal berlaku hingga harus diisi',
            'berlaku_hingga.after' => 'Tanggal berlaku hingga harus setelah tanggal berlaku mulai',
            'status.required' => 'Status harus dipilih',
        ]);

        // Simpan data original
        $originalData = $member->toArray();

        $member->update([
            'id_pemilik'     => $request->id_pemilik,
            'id_level'       => $request->id_level,
            'berlaku_mulai'  => $request->berlaku_mulai,
            'berlaku_hingga' => $request->berlaku_hingga,
            'status'         => $request->status,
        ]);
        
        // Refresh relasi
        $member->load(['pemilik', 'level']);
        
        // ← LOG ACTIVITY
        $this->logUpdate($member, 'member', $originalData, [
            'pemilik' => $member->pemilik->nama ?? null,
            'level' => $member->level->nama_level ?? null,
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
        
        // Validasi: Cek apakah pemilik sudah terdaftar sebagai member aktif
        $existingMember = Member::where('id_pemilik', $member->id_pemilik)->first();
        
        if ($existingMember) {
            return redirect()
                ->route('member.trash')
                ->with('error', 'Gagal! Pemilik "' . ($member->pemilik->nama ?? 'ini') . '" sudah terdaftar sebagai member aktif.');
        }
        
        $member->restore();
        
        // Load relasi
        $member->load(['pemilik', 'level']);
        
        // ← LOG ACTIVITY
        $this->logRestore($member, 'member', [
            'pemilik' => $member->pemilik->nama ?? null,
            'level' => $member->level->nama_level ?? null,
        ]);

        return redirect()
        ->route('member.trash')
        ->with('success', 'Member berhasil dikembalikan');
    }

    public function destroy(Member $member)
    {
        // Load relasi sebelum delete
        $member->load(['pemilik', 'level']);
        
        // ← LOG ACTIVITY (SEBELUM DELETE!)
        $this->logDelete($member, 'member', [
            'pemilik' => $member->pemilik->nama ?? null,
            'level' => $member->level->nama_level ?? null,
        ]);
        
        $member->delete(); // soft delete
        
        return redirect()->route('member.index')
            ->with('success', 'Member berhasil dihapus');
    }
}