<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogPageNavigation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only log for authenticated users
        if (Auth::check()) {
            // Skip logging for these routes/patterns
            $skipRoutes = [
                'activity-log.*',           // Don't log activity log views (infinite loop!)
                '*.autocomplete',           // Skip autocomplete
                '*.fetch',                  // Skip fetch requests
                'logout',                   // Skip logout (handled separately)
            ];
            
            // Skip AJAX requests
            if ($request->ajax()) {
                return $next($request);
            }
            
            // Skip if route matches skip patterns
            $currentRoute = $request->route()->getName();
            
            foreach ($skipRoutes as $pattern) {
                if (fnmatch($pattern, $currentRoute)) {
                    return $next($request);
                }
            }
            
            // Log the navigation AFTER request is processed
            $response = $next($request);
            
            // Only log successful GET requests
            if ($request->isMethod('GET') && $response->isSuccessful()) {
                $this->logNavigation($request);
            }
            
            return $response;
        }
        
        return $next($request);
    }
    
    /**
     * Log the navigation
     */
    private function logNavigation(Request $request)
    {
        try {
            $routeName = $request->route()->getName() ?? 'unknown';
            $routeParams = $request->route()->parameters();
            
            // Build friendly description
            $description = $this->buildDescription($routeName, $routeParams);
            
            ActivityLog::logNavigation($routeName, $request->fullUrl());
            
        } catch (\Exception $e) {
            // Silently fail - don't break the application
            \Log::error('Failed to log navigation: ' . $e->getMessage());
        }
    }
    
    /**
     * Build friendly description from route name
     */
    private function buildDescription(string $routeName, array $params): string
    {
        $descriptions = [
            'dashboard.index' => 'Membuka Dashboard',
            'user.index' => 'Melihat Daftar User',
            'user.create' => 'Membuka Form Tambah User',
            'user.edit' => 'Membuka Form Edit User',
            'roles.index' => 'Melihat Daftar Role',
            'tipe-kendaraan.index' => 'Melihat Daftar Tipe Kendaraan',
            'area-parkir.index' => 'Melihat Daftar Area Parkir',
            'area-kapasitas.index' => 'Melihat Kapasitas Area Parkir',
            'data-kendaraan.index' => 'Melihat Daftar Kendaraan',
            'pemilik.index' => 'Melihat Daftar Pemilik',
            'member.index' => 'Melihat Daftar Member',
            'tarif-parkir.index' => 'Melihat Daftar Tarif Parkir',
            'metode-pembayaran.index' => 'Melihat Daftar Metode Pembayaran',
            'detail-parkir.index' => 'Melihat Detail Parkir',
            'parkir.masuk' => 'Membuka Form Parkir Masuk',
            'parkir.keluar' => 'Membuka Form Parkir Keluar',
            'laporan.breakdown' => 'Melihat Laporan Breakdown',
            'master-data.data-parkir' => 'Melihat Master Data Parkir',
            'master-data.riwayat-transaksi' => 'Melihat Riwayat Transaksi',
            'master-data.member-kendaraan' => 'Melihat Data Member & Kendaraan',
        ];
        
        return $descriptions[$routeName] ?? "Mengakses: {$routeName}";
    }
}