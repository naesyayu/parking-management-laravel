<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        // Build query
        $query = ActivityLog::with(['user', 'transaksi.kendaraan'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('id_user') && $request->id_user !== 'all') {
            $query->where('id_user', $request->id_user);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        // Pagination
        $logs = $query->paginate(50)->appends($request->all());

        // Get all users for filter
        $users = User::where('status', 'aktif')
            ->orderBy('username')
            ->get();

        // Available actions (UPDATED dengan CRUD lengkap)
        $actions = [
            // Auth
            'login' => 'Login',
            'logout' => 'Logout',
            
            // Transaksi
            'transaksi_masuk' => 'Transaksi Masuk',
            'transaksi_keluar' => 'Transaksi Keluar',
            'cetak_struk' => 'Cetak Struk',
            
            // Kendaraan
            'tambah_kendaraan' => 'Tambah Kendaraan',
            'edit_kendaraan' => 'Edit Kendaraan',
            'hapus_kendaraan' => 'Hapus Kendaraan',
            'restore_kendaraan' => 'Restore Kendaraan',
            
            // Member
            'tambah_member' => 'Tambah Member',
            'edit_member' => 'Edit Member',
            'hapus_member' => 'Hapus Member',
            'restore_member' => 'Restore Member',
            
            // Tarif
            'tambah_tarif' => 'Tambah Tarif',
            'edit_tarif' => 'Edit Tarif',
            'hapus_tarif' => 'Hapus Tarif',
            'restore_tarif' => 'Restore Tarif',
            
            // Pemilik
            'tambah_pemilik' => 'Tambah Pemilik',
            'edit_pemilik' => 'Edit Pemilik',
            'hapus_pemilik' => 'Hapus Pemilik',
            'restore_pemilik' => 'Restore Pemilik',
            
            // Area Parkir
            'tambah_area_parkir' => 'Tambah Area Parkir',
            'edit_area_parkir' => 'Edit Area Parkir',
            'hapus_area_parkir' => 'Hapus Area Parkir',
            'restore_area_parkir' => 'Restore Area Parkir',
            
            // Tipe Kendaraan
            'tambah_tipe_kendaraan' => 'Tambah Tipe Kendaraan',
            'edit_tipe_kendaraan' => 'Edit Tipe Kendaraan',
            'hapus_tipe_kendaraan' => 'Hapus Tipe Kendaraan',
            
            // Metode Pembayaran
            'tambah_metode_pembayaran' => 'Tambah Metode Pembayaran',
            'edit_metode_pembayaran' => 'Edit Metode Pembayaran',
            'hapus_metode_pembayaran' => 'Hapus Metode Pembayaran',
            'restore_metode_pembayaran' => 'Restore Metode Pembayaran',
            
            // User & Role
            'tambah_user' => 'Tambah User',
            'edit_user' => 'Edit User',
            'hapus_user' => 'Hapus User',
            'restore_user' => 'Restore User',
            'edit_password' => 'Edit Password',
            
            'tambah_role' => 'Tambah Role',
            'edit_role' => 'Edit Role',
            'hapus_role' => 'Hapus Role',
            
            // Laporan
            'export_laporan' => 'Export Laporan',
            
            // Other
            'other' => 'Lainnya',
        ];

        return view('pages/activity-log.index', compact('logs', 'users', 'actions'));
    }

    /**
     * Show the form for creating a new resource (tidak digunakan)
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource (tidak digunakan - log otomatis)
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource
     */
    public function show(string $id)
    {
        $log = ActivityLog::with(['user', 'transaksi.kendaraan'])->findOrFail($id);

        return view('activity-log.show', compact('log'));
    }

    /**
     * Clear old logs (optional - untuk maintenance)
     */
    public function clearOldLogs(Request $request)
    {
        // Hanya admin yang bisa
        if (!auth()->user()->role->isAdmin()) {
            return back()->with('error', 'Hanya admin yang dapat menghapus log');
        }

        $request->validate([
            'days' => 'required|integer|min:30',
        ]);

        $date = Carbon::now()->subDays($request->days);

        $deleted = ActivityLog::where('created_at', '<', $date)->delete();

        ActivityLog::log('other', "Menghapus {$deleted} log lama (lebih dari {$request->days} hari)", null, [
            'days' => $request->days,
            'deleted_count' => $deleted,
        ]);

        return back()->with('success', "Berhasil menghapus {$deleted} log lama");
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        // Build query (sama seperti index)
        $query = ActivityLog::with(['user', 'transaksi.kendaraan'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->filled('id_user') && $request->id_user !== 'all') {
            $query->where('id_user', $request->id_user);
        }

        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        $logs = $query->get();

        // Generate CSV
        $filename = 'activity-log-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'ID',
                'Tanggal/Waktu',
                'User',
                'Action',
                'Deskripsi',
                'Transaksi ID',
                'IP Address',
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id_log,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->user?->username ?? 'System',
                    $log->action,
                    $log->description,
                    $log->id_transaksi ?? '-',
                    $log->ip_address ?? '-',
                ]);
            }

            fclose($file);
        };

        // Log activity
        ActivityLog::log('export_laporan', 'Export activity log', null, [
            'total_records' => $logs->count(),
            'filters' => $request->all(),
        ]);

        return response()->stream($callback, 200, $headers);
    }
}