<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiParkir;
use App\Models\Kendaraan;
use App\Models\AreaKapasitas;
use App\Models\TipeKendaraan;
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

    /**
     * AUTOCOMPLETE PLAT NOMOR
     * PERBAIKAN:
     * - Minimal 1 karakter (tidak perlu 2)
     * - SPASI TIDAK DIHAPUS (karena di database ada spasi)
     * - Hanya uppercase
     */
    public function autocompletePlat(Request $request)
    {
        try {
            // Ambil keyword, trim whitespace di awal/akhir, uppercase
            // TIDAK menghapus spasi di tengah!
            $keyword = strtoupper(trim($request->get('q', '')));

            // PERBAIKAN: Minimal 1 karakter saja (bukan 2)
            if (strlen($keyword) < 1) {
                return response()->json([]);
            }

            Log::info('Autocomplete search:', ['keyword' => $keyword]);

            // Cari kendaraan dengan plat yang match
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

        // Validasi
        $request->validate([
            'plat_nomor' => 'required|string|max:13',
            'id_tipe' => 'required|exists:tipe_kendaraan,id_tipe',
        ]);

        DB::beginTransaction();

        try {
            // PERBAIKAN: Normalisasi plat nomor
            // HANYA uppercase, TIDAK menghapus spasi
            // Trim hanya whitespace di awal/akhir
            $platNomor = strtoupper(trim($request->plat_nomor));
            
            Log::info('Normalized plat:', ['plat' => $platNomor, 'length' => strlen($platNomor)]);

            // Cek sedang parkir
            $sedangParkir = TransaksiParkir::whereHas('kendaraan', function($q) use ($platNomor) {
                $q->where('plat_nomor', $platNomor);
            })->where('status', 'in')->first();

            if ($sedangParkir) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', 'Kendaraan ' . $platNomor . ' masih parkir');
            }

            // Cek/buat kendaraan
            $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

            if ($kendaraan) {
                // Kendaraan sudah ada
                if ($kendaraan->id_tipe != $request->id_tipe) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'Plat ' . $platNomor . ' terdaftar sebagai ' . $kendaraan->tipe->tipe_kendaraan);
                }
                Log::info('Using existing vehicle:', ['id' => $kendaraan->id_kendaraan]);
            } else {
                // Kendaraan baru - simpan dengan spasi seperti yang diinput
                $kendaraan = Kendaraan::create([
                    'plat_nomor' => $platNomor,
                    'id_tipe' => $request->id_tipe,
                    'id_pemilik' => null,
                    'status' => 'aktif',
                ]);
                Log::info('Created new vehicle:', ['id' => $kendaraan->id_kendaraan, 'plat' => $platNomor]);
            }

            // Cari slot parkir
            $kapasitas = AreaKapasitas::lockForUpdate()
                ->where('id_tipe', $request->id_tipe)
                ->where('kapasitas', '>', 0)
                ->orderBy('kapasitas', 'desc')
                ->first();

            if (!$kapasitas) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', 'Slot parkir penuh untuk tipe ini');
            }

            Log::info('Slot found:', ['area' => $kapasitas->id_area]);

            // Generate kode tiket
            $kodeTiket = $this->generateKodeTiket();
            Log::info('Generated ticket:', ['kode' => $kodeTiket]);

            // Handle user ID dengan aman
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

    /**
     * Generate QR Code dengan fallback
     */
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

    /**
     * Generate placeholder QR
     */
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

    /**
     * Generate kode tiket unik
     */
    private function generateKodeTiket()
    {
        do {
            $kode = 'TK' . now()->format('YmdHis') . rand(100, 999);
        } while (TransaksiParkir::where('kode_tiket', $kode)->exists());

        return $kode;
    }
}