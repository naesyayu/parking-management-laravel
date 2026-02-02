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
        // Redirect jika sudah login
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

        // Ambil credentials
        $credentials = $request->only('username', 'password');

        // Cek apakah user ada
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username tidak ditemukan');
        }

        // Cek status user
        if ($user->status !== 'aktif') {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator');
        }

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Log activity
            ActivityLog::log('login', 'User berhasil login');

            // Redirect ke dashboard
            return redirect()->route('dashboard.index')
                ->with('success', 'Selamat datang, ' . Auth::user()->username . '!');
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
        // Log activity sebelum logout
        ActivityLog::log('logout', 'User logout');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }
}