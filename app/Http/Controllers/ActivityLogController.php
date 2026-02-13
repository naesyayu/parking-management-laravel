<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        // Build query
        $query = ActivityLog::with(['user.role', 'transaksi.kendaraan'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('id_user') && $request->id_user !== 'all') {
            $query->where('id_user', $request->id_user);
        }
        
        // Filter by search (username or description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('username', 'like', "%{$search}%");
                  });
            });
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

        // Get all users for filter (including deleted users that have logs)
        $users = User::withTrashed()
            ->orderBy('username')
            ->get();

        // Available actions (UPDATED dengan semua actions)
        $actions = $this->getAvailableActions();

        return view('pages.activity-log.index', compact('logs', 'users', 'actions'));
    }

    /**
     * Display the specified resource
     */
    public function show(string $id)
    {
        $log = ActivityLog::with(['user.role', 'transaksi.kendaraan.tipe'])->findOrFail($id);

        return view('pages.activity-log.show', compact('log'));
    }

    /**
     * Export logs to Excel (Enhanced)
     */
    public function export(Request $request)
    {
        // Build query (sama seperti index)
        $query = ActivityLog::with(['user.role', 'transaksi.kendaraan'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->filled('id_user') && $request->id_user !== 'all') {
            $query->where('id_user', $request->id_user);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('username', 'like', "%{$search}%");
                  });
            });
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

        // ========================================
        // CREATE EXCEL WITH PHPSPREADSHEET
        // ========================================
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set metadata
        $spreadsheet->getProperties()
            ->setCreator('Parking Management System')
            ->setTitle('Activity Log Report')
            ->setSubject('Activity Log')
            ->setDescription('Activity log export from parking management system');
        
        // ========================================
        // TITLE & HEADER
        // ========================================
        
        // Title
        $sheet->setCellValue('A1', 'LAPORAN ACTIVITY LOG');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Export info
        $sheet->setCellValue('A2', 'Diekspor pada: ' . now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Filter info
        $filterInfo = 'Filter: ';
        if ($request->filled('start_date')) {
            $filterInfo .= 'Dari ' . Carbon::parse($request->start_date)->format('d/m/Y') . ' ';
        }
        if ($request->filled('end_date')) {
            $filterInfo .= 'Sampai ' . Carbon::parse($request->end_date)->format('d/m/Y') . ' ';
        }
        if ($request->filled('action') && $request->action !== 'all') {
            $filterInfo .= 'Action: ' . $request->action . ' ';
        }
        
        $sheet->setCellValue('A3', $filterInfo);
        $sheet->mergeCells('A3:J3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getFont()->setItalic(true);
        
        // Empty row
        $row = 5;
        
        // Column headers
        $headers = [
            'A' => 'No',
            'B' => 'Tanggal/Waktu',
            'C' => 'Username',
            'D' => 'Role',
            'E' => 'Action',
            'F' => 'Deskripsi',
            'G' => 'IP Address',
            'H' => 'Changes',
            'I' => 'Transaksi ID',
            'J' => 'URL',
        ];
        
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($headerStyle);
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // ========================================
        // DATA ROWS
        // ========================================
        
        $row++;
        $no = 1;
        
        foreach ($logs as $log) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $log->created_at->format('d/m/Y H:i:s'));
            $sheet->setCellValue('C' . $row, $log->user?->username ?? 'Guest/System');
            $sheet->setCellValue('D' . $row, $log->user?->role?->role_user ?? '-');
            $sheet->setCellValue('E' . $row, $log->action);
            $sheet->setCellValue('F' . $row, $log->description ?? '-');
            $sheet->setCellValue('G' . $row, $log->ip_address ?? '-');
            
            // Changes column
            if (isset($log->metadata['changes']) && count($log->metadata['changes']) > 0) {
                $changesText = '';
                foreach ($log->metadata['changes'] as $field => $change) {
                    $from = $change['from'] ?? 'null';
                    $to = $change['to'] ?? 'null';
                    $changesText .= "{$field}: {$from} → {$to}\n";
                }
                $sheet->setCellValue('H' . $row, trim($changesText));
            } else {
                $sheet->setCellValue('H' . $row, '-');
            }
            
            $sheet->setCellValue('I' . $row, $log->id_transaksi ?? '-');
            $sheet->setCellValue('J' . $row, $log->metadata['url'] ?? '-');
            
            // Alternate row colors
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2F2F2');
            }
            
            // Borders
            $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Wrap text for description and changes
            $sheet->getStyle('F' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
            
            $row++;
        }
        
        // ========================================
        // COLUMN WIDTHS
        // ========================================
        
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(50);
        
        // ========================================
        // SUMMARY
        // ========================================
        
        $row += 2;
        $sheet->setCellValue('A' . $row, 'RINGKASAN:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Records: ' . $logs->count());
        
        $row++;
        $actionCounts = $logs->groupBy('action')->map->count();
        $sheet->setCellValue('A' . $row, 'Breakdown by Action:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        foreach ($actionCounts as $action => $count) {
            $sheet->setCellValue('A' . $row, "  - {$action}: {$count} records");
            $row++;
        }
        
        // ========================================
        // GENERATE FILE
        // ========================================
        
        $filename = 'activity-log-' . date('Y-m-d-His') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        
        // Log activity
        ActivityLog::log(
            'export_laporan',
            'Export activity log ke Excel',
            null,
            [
                'total_records' => $logs->count(),
                'format' => 'xlsx',
                'filters' => $request->all(),
            ]
        );
        
        exit;
    }
    
    /**
     * Get all available actions
     */
    private function getAvailableActions(): array
    {
        return [
            // Auth
            'login' => 'Login',
            'login_failed' => 'Login Gagal',
            'logout' => 'Logout',
            
            // Navigation
            'page_view' => 'Navigasi Halaman',
            
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
            'tambah_tarif_parkir' => 'Tambah Tarif',
            'edit_tarif_parkir' => 'Edit Tarif',
            'hapus_tarif_parkir' => 'Hapus Tarif',
            'restore_tarif_parkir' => 'Restore Tarif',
            
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
            
            // Area Kapasitas
            'tambah_area_kapasitas' => 'Tambah Area Kapasitas',
            'edit_area_kapasitas' => 'Edit Area Kapasitas',
            'hapus_area_kapasitas' => 'Hapus Area Kapasitas',
            
            // Tipe Kendaraan
            'tambah_tipe_kendaraan' => 'Tambah Tipe Kendaraan',
            'edit_tipe_kendaraan' => 'Edit Tipe Kendaraan',
            'hapus_tipe_kendaraan' => 'Hapus Tipe Kendaraan',
            'restore_tipe_kendaraan' => 'Restore Tipe Kendaraan',
            
            // Metode Pembayaran
            'tambah_metode_pembayaran' => 'Tambah Metode Pembayaran',
            'edit_metode_pembayaran' => 'Edit Metode Pembayaran',
            'hapus_metode_pembayaran' => 'Hapus Metode Pembayaran',
            'restore_metode_pembayaran' => 'Restore Metode Pembayaran',
            
            // Detail Parkir
            'tambah_detail_parkir' => 'Tambah Detail Parkir',
            'edit_detail_parkir' => 'Edit Detail Parkir',
            'hapus_detail_parkir' => 'Hapus Detail Parkir',
            'restore_detail_parkir' => 'Restore Detail Parkir',
            
            // User & Role
            'tambah_user' => 'Tambah User',
            'edit_user' => 'Edit User',
            'hapus_user' => 'Hapus User',
            'restore_user' => 'Restore User',
            'edit_password' => 'Edit Password',
            
            'tambah_role' => 'Tambah Role',
            'edit_role' => 'Edit Role',
            'hapus_role' => 'Hapus Role',
            'restore_role' => 'Restore Role',
            
            // Laporan
            'export_laporan' => 'Export Laporan',
            
            // Other
            'other' => 'Lainnya',
        ];
    }
}