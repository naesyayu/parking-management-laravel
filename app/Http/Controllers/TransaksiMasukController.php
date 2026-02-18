<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiParkir;
use App\Models\Kendaraan;
use App\Models\AreaKapasitas;
use App\Models\TipeKendaraan;
use App\Models\ActivityLog;
use App\Rules\PlatNomorIndonesia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;

class TransaksiMasukController extends Controller
{
    public function index()
    {
        try {
            $kapasitas = AreaKapasitas::with(['area', 'tipe'])
                ->where('kapasitas', '>', 0)
                ->get()
                ->groupBy('id_tipe');

            return view('pages.parkir.masuk', [
                'tipe' => TipeKendaraan::all(),
                'kapasitas' => $kapasitas,
            ]);
        } catch (\Exception $e) {
            Log::error('Index error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat halaman: ' . $e->getMessage());
        }
    }

    public function autocompletePlat(Request $request)
    {
        try {
            $keyword = strtoupper(trim($request->get('q', '')));

            if (strlen($keyword) < 1) {
                return response()->json([]);
            }

            Log::info('Autocomplete search:', ['keyword' => $keyword]);

            $data = Kendaraan::with('tipe')
                ->where('plat_nomor', 'like', "%{$keyword}%")
                ->where('status', 'aktif')
                ->limit(10)
                ->get()
                ->map(function ($kendaraan) {
                    return [
                        'plat_nomor' => $kendaraan->plat_nomor,
                        'id_tipe' => $kendaraan->id_tipe,
                        'tipe_kendaraan' => $kendaraan->tipe->tipe_kendaraan,
                    ];
                });

            Log::info('Autocomplete results:', ['count' => $data->count()]);

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('Autocomplete error: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function store(Request $request)
    {
        Log::info('=== TRANSAKSI MASUK START ===');
        Log::info('Request data:', $request->all());

        $request->validate([
            'plat_nomor' => ['required', 'string', 'max:20', new PlatNomorIndonesia],
            'id_tipe' => 'required|exists:tipe_kendaraan,id_tipe',
            'id_area_manual' => 'nullable|exists:area_parkir,id_area',
        ], [
            'plat_nomor.required' => 'Plat nomor harus diisi',
            'plat_nomor.max' => 'Plat nomor terlalu panjang',
            'id_tipe.required' => 'Tipe kendaraan harus dipilih',
            'id_tipe.exists' => 'Tipe kendaraan tidak valid',
            'id_area_manual.exists' => 'Area parkir tidak valid',
        ]);

        DB::beginTransaction();

        try {
            // Normalize plat nomor menggunakan PlatNomorIndonesia rule
            $platNomor = PlatNomorIndonesia::normalize($request->plat_nomor);
            
            Log::info('Normalized plat:', ['plat' => $platNomor, 'length' => strlen($platNomor)]);

            // Cek sedang parkir
            $sedangParkir = TransaksiParkir::whereHas('kendaraan', function($q) use ($platNomor) {
                $q->where('plat_nomor', $platNomor);
            })->where('status', 'in')->first();

            if ($sedangParkir) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', 'Kendaraan ' . $platNomor . ' masih parkir di area ' . $sedangParkir->areaParkir->nama_area);
            }

            // Cek/buat kendaraan
            $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

            if ($kendaraan) {
                if ($kendaraan->id_tipe != $request->id_tipe) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'Plat ' . $platNomor . ' terdaftar sebagai ' . $kendaraan->tipe->tipe_kendaraan . ', bukan ' . TipeKendaraan::find($request->id_tipe)->tipe_kendaraan);
                }
                Log::info('Using existing vehicle:', ['id' => $kendaraan->id_kendaraan]);
            } else {
                $kendaraan = Kendaraan::create([
                    'plat_nomor' => $platNomor,
                    'id_tipe' => $request->id_tipe,
                    'id_pemilik' => null,
                    'status' => 'aktif',
                ]);
                Log::info('Created new vehicle:', ['id' => $kendaraan->id_kendaraan, 'plat' => $platNomor]);
            }

            // ========================================
            // CARI SLOT PARKIR - WITH AREA SELECTION
            // ========================================
            if ($request->filled('id_area_manual')) {
                // User selected specific area
                $kapasitas = AreaKapasitas::lockForUpdate()
                    ->where('id_area', $request->id_area_manual)
                    ->where('id_tipe', $request->id_tipe)
                    ->where('kapasitas', '>', 0)
                    ->first();
                
                if (!$kapasitas) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'Slot parkir penuh untuk area yang dipilih');
                }
            } else {
                // Auto select area dengan slot paling banyak
                $kapasitas = AreaKapasitas::lockForUpdate()
                    ->where('id_tipe', $request->id_tipe)
                    ->where('kapasitas', '>', 0)
                    ->orderBy('kapasitas', 'desc')
                    ->first();
                
                if (!$kapasitas) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'Slot parkir penuh untuk tipe kendaraan ini');
                }
            }

            Log::info('Slot found:', [
                'area' => $kapasitas->id_area, 
                'lokasi' => $kapasitas->area->nama_area,
                'remaining' => $kapasitas->kapasitas
            ]);

            // Generate kode tiket
            $kodeTiket = $this->generateKodeTiket();
            Log::info('Generated ticket:', ['kode' => $kodeTiket]);

            // Handle user ID
            $userId = null;
            try {
                if (auth()->check()) {
                    $userId = auth()->id();
                }
            } catch (\Exception $e) {
                Log::warning('Auth check failed: ' . $e->getMessage());
            }

            Log::info('User ID:', ['id_user' => $userId]);

            // Simpan transaksi
            $transaksi = TransaksiParkir::create([
                'kode_tiket' => $kodeTiket,
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'id_area' => $kapasitas->id_area,
                'waktu_masuk' => now(),
                'waktu_keluar' => null,
                'durasi_jam' => null,
                'id_tarif' => null,
                'id_user' => $userId,
                'id_member' => null,
                'id_metode' => null,
                'status' => 'in',
            ]);

            Log::info('Transaction saved:', ['id' => $transaksi->id_transaksi]);

            // Kurangi kapasitas
            $kapasitas->decrement('kapasitas');

            // LOG ACTIVITY - TRANSAKSI MASUK
            ActivityLog::create([
                'id_user' => $userId,
                'action' => 'transaksi_masuk',
                'description' => "Transaksi masuk: {$platNomor} ({$kendaraan->tipe->tipe_kendaraan}) - {$kapasitas->area->nama_area}",
                'id_transaksi' => $transaksi->id_transaksi,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => json_encode([
                    'kode_tiket' => $kodeTiket,
                    'plat_nomor' => $platNomor,
                    'tipe_kendaraan' => $kendaraan->tipe->tipe_kendaraan,
                    'area' => $kapasitas->area->nama_area,
                    'waktu_masuk' => now()->format('Y-m-d H:i:s'),
                ])
            ]);

            DB::commit();
            Log::info('Transaction committed');

            // Generate QR Code
            $qrCodeBase64 = $this->generateQrCode($kodeTiket);

            // Load relasi
            try {
                $transaksi->load(['kendaraan.tipe', 'areaParkir']);
                
                if ($userId) {
                    $transaksi->load('user');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to load relations: ' . $e->getMessage());
            }

            Log::info('=== TRANSAKSI MASUK SUCCESS ===');

            return view('pages.parkir.tiket-masuk', [
                'transaksi' => $transaksi,
                'qr' => $qrCodeBase64,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('=== TRANSAKSI MASUK ERROR ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'ERROR: ' . $e->getMessage());
        }
    }

    public function cetakTiket(Request $request)
    {
        try {
            $idTransaksi = $request->input('id_transaksi');
            
            $transaksi = TransaksiParkir::with(['kendaraan.tipe', 'areaParkir'])
                ->findOrFail($idTransaksi);

            ActivityLog::create([
                'id_user' => auth()->id(),
                'action' => 'cetak_struk',
                'description' => "Cetak ulang tiket: {$transaksi->kode_tiket} - {$transaksi->kendaraan->plat_nomor}",
                'id_transaksi' => $transaksi->id_transaksi,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => json_encode([
                    'kode_tiket' => $transaksi->kode_tiket,
                    'plat_nomor' => $transaksi->kendaraan->plat_nomor,
                    'cetak_ulang' => true,
                ])
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Cetak tiket error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function generateQrCode($kodeTiket)
    {
        try {
            if (class_exists('Endroid\QrCode\QrCode')) {
                $qrCode = new QrCode(
                    data: $kodeTiket,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::High,
                    size: 300,
                    margin: 10,
                    foregroundColor: new Color(0, 0, 0),
                    backgroundColor: new Color(255, 255, 255)
                );

                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                return base64_encode($result->getString());
            }
            
            $url = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($kodeTiket);
            $imageData = @file_get_contents($url);
            
            if ($imageData) {
                return base64_encode($imageData);
            }
            
            return $this->generatePlaceholderQr($kodeTiket);
            
        } catch (\Exception $e) {
            Log::error('QR generation error: ' . $e->getMessage());
            return $this->generatePlaceholderQr($kodeTiket);
        }
    }

    private function generatePlaceholderQr($text)
    {
        $img = imagecreate(300, 300);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        imagefill($img, 0, 0, $white);
        imagestring($img, 5, 100, 140, $text, $black);
        
        ob_start();
        imagepng($img);
        $imageData = ob_get_clean();
        imagedestroy($img);
        
        return base64_encode($imageData);
    }

    private function generateKodeTiket()
    {
        do {
            $kode = 'TK' . now()->format('YmdHis') . rand(100, 999);
        } while (TransaksiParkir::where('kode_tiket', $kode)->exists());

        return $kode;
    }
}