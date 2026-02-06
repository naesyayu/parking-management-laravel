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
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has role
        if (!$user->role) {
            return response()->view('403', [], 403);
        }

        // Normalize role names for comparison
        $userRole = strtolower(str_replace(' ', '', $user->role->role_user)); // 'petugasparkir'
        $allowedRoles = array_map(function($role) {
            return strtolower(str_replace(' ', '', $role));
        }, $roles);

        // Check if user role is in allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            return response()->view('403', [], 403);
        }

        return $next($request);
    }
}