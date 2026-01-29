<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = User::with('role')->get();
        return view('user.index', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'id_role' => 'required',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_role' => $request->id_role,
            'status' => 'aktif',
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id_user . ',id_user',
            'id_role' => 'required',
            'status' => 'required',
        ]);

        $user->update([
            'username' => $request->username,
            'id_role' => $request->id_role,
            'status' => $request->status,
        ]);

        return redirect()->route('user.index')
            ->with('success', 'User berhasil diperbarui');
    }

    // 🔹 DATA TERHAPUS (BACKUP)
    public function trash()
    {
        $user = User::onlyTrashed()->with('role')->get();
        return view('user.trash', compact('user'));
    }


    // 🔹 RESTORE USER
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();


        return redirect()
        ->route('user.trash')
        ->with('success', 'User berhasil dikembalikan');
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }

    /* FORM UPDATE PASSWORD */
    public function editPassword(User $user)
    {
        return view('user.password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.index')->with('success', 'Password berhasil diubah');
    }
}
