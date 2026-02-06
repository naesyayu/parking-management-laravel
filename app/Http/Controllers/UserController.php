<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\ActivityLogger;

class UserController extends Controller
{
    use ActivityLogger;
    
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

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_role' => $request->id_role,
            'status' => 'aktif',
        ]);
        
        // Load relasi
        $user->load('role');
        
        $this->logCreate($user, 'user', [
            'role' => $user->role->role_user ?? null,
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

        $originalData = $user->toArray();
        
        $user->update([
            'username' => $request->username,
            'id_role' => $request->id_role,
            'status' => $request->status,
        ]);
        
        // Refresh relasi
        $user->load('role');
        
        $this->logUpdate($user, 'user', $originalData, [
            'role' => $user->role->role_user ?? null,
        ]);

        return redirect()->route('user.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function trash()
    {
        $user = User::onlyTrashed()->with('role')->get();
        return view('user.trash', compact('user'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        
        // Load relasi
        $user->load('role');
        
        $this->logRestore($user, 'user', [
            'role' => $user->role->role_user ?? null,
        ]);

        return redirect()
            ->route('user.trash')
            ->with('success', 'User berhasil dikembalikan');
    }

    public function destroy(User $user)
    {
        // Load relasi sebelum delete
        $user->load('role');
        
        $this->logDelete($user, 'user', [
            'role' => $user->role->role_user ?? null,
        ]);
        
        $user->delete();
        
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }

    // ========================================
    // UBAH PASSWORD USER LAIN (ADMIN ONLY)
    // ========================================

    /**
     * Form ubah password user lain (hanya Admin)
     */
    public function editPassword(User $user)
    {
        // CRITICAL: Admin TIDAK BISA ubah password sendiri via CRUD
        if ($user->id_user === Auth::id()) {
            return redirect()
                ->route('password.change')
                ->with('warning', 'Anda tidak dapat mengubah password sendiri di sini. Silakan gunakan halaman "Ubah Password" dengan verifikasi password lama.');
        }

        // Check role Admin
        if (!Auth::user()->role->isAdmin()) {
            return redirect()
                ->back()
                ->with('error', 'Hanya Admin yang dapat mengubah password user lain');
        }

        return view('user.password', compact('user'));
    }

    /**
     * Update password user lain (hanya Admin, tanpa password lama)
     */
    public function updatePassword(Request $request, User $user)
    {
        // CRITICAL: Admin TIDAK BISA ubah password sendiri via CRUD
        if ($user->id_user === Auth::id()) {
            return redirect()
                ->route('password.change')
                ->with('warning', 'Anda tidak dapat mengubah password sendiri di sini. Silakan gunakan halaman "Ubah Password".');
        }

        // Check role Admin
        if (!Auth::user()->role->isAdmin()) {
            return redirect()
                ->back()
                ->with('error', 'Hanya Admin yang dapat mengubah password user lain');
        }

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        // LOG EDIT PASSWORD
        \App\Models\ActivityLog::log(
            'edit_password', 
            'Admin mengubah password user: ' . $user->username,
            null,
            ['target_user_id' => $user->id_user]
        );

        return redirect()
            ->route('user.index')
            ->with('success', 'Password user ' . $user->username . ' berhasil diubah');
    }

    // ========================================
    // UBAH PASSWORD SENDIRI (SEMUA USER)
    // ========================================

    /**
     * Form ubah password sendiri (dengan verifikasi password lama)
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        return view('user.change-password', compact('user'));
    }

    /**
     * Update password sendiri (dengan verifikasi password lama)
     */
    public function updateOwnPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok',
        ]);

        $user = Auth::user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log activity
        \App\Models\ActivityLog::log(
            'edit_password', 
            'User mengubah password sendiri: ' . $user->username
        );

        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Password Anda berhasil diubah');
    }
}