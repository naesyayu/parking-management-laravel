<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return view('login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username tidak ditemukan');
        }

        if ($user->status !== 'aktif') {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Log activity
            ActivityLog::log('login', 'User berhasil login');

            // NOTIFIKASI: Simpan username untuk ditampilkan di dashboard
            $username = Auth::user()->username;
            
            return redirect()->route('dashboard.index')
                ->with('login_success', true)
                ->with('username', $username);
        }

        return back()
            ->withInput($request->only('username'))
            ->with('error', 'Password salah');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'User logout');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }
}