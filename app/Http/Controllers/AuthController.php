<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');
        $remember = $request->filled('remember');

        // ========================================
        // ATTEMPT LOGIN
        // ========================================
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active
            if ($user->status !== 'aktif') {
                Auth::logout();
                
                // Log failed login (inactive user)
                ActivityLog::logFailedLogin(
                    $request->username,
                    'User account is inactive'
                );
                
                return back()
                    ->withInput($request->only('username'))
                    ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            }
            
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();
            
            // ========================================
            // LOG SUCCESSFUL LOGIN
            // ========================================
            ActivityLog::create([
                'id_user' => $user->id_user,
                'action' => 'login',
                'description' => "User {$user->username} berhasil login",
                'metadata' => [
                    'username' => $user->username,
                    'role' => $user->role->role_user ?? 'Unknown',
                    'login_time' => now()->toIso8601String(),
                    'remember_me' => $remember,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // ========================================
            // SET SESSION FOR NOTIFICATION
            // IMPORTANT: Set these BEFORE redirect
            // ========================================
            session()->flash('login_success', true);
            session()->flash('username', $user->username);
            session()->flash('role', $user->role->role_user ?? 'User');
            
            // Alternative: Using with() method (both work)
            return redirect()
                ->intended(route('dashboard.index'))
                ->with([
                    'login_success' => true,
                    'username' => $user->username,
                    'role' => $user->role->role_user ?? 'User'
                ]);
        }
        
        // ========================================
        // LOG FAILED LOGIN ATTEMPT
        // ========================================
        
        // Check if user exists
        $userExists = User::where('username', $request->username)->exists();
        
        if ($userExists) {
            // User exists but wrong password
            ActivityLog::logFailedLogin(
                $request->username,
                'Invalid password'
            );
            
            $errorMessage = 'Password salah';
        } else {
            // User doesn't exist
            ActivityLog::logFailedLogin(
                $request->username,
                'Username not found'
            );
            
            $errorMessage = 'Username tidak ditemukan';
        }
        
        return back()
            ->withInput($request->only('username'))
            ->with('error', $errorMessage);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // ========================================
        // LOG LOGOUT
        // ========================================
        if ($user) {
            ActivityLog::create([
                'id_user' => $user->id_user,
                'action' => 'logout',
                'description' => "User {$user->username} logout",
                'metadata' => [
                    'username' => $user->username,
                    'logout_time' => now()->toIso8601String(),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Anda telah logout');
    }
}