<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // Check if user has role
        if (!$user->role) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak ditemukan');
        }

        // Normalize role names for comparison
        $userRole = strtolower(str_replace(' ', '', $user->role->role_user)); // 'petugasparkir'
        $allowedRoles = array_map(function($role) {
            return strtolower(str_replace(' ', '', $role));
        }, $roles);

        // Check if user role is in allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}