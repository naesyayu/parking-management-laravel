<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiParkir;
use App\Models\TarifParkir;
use App\Models\DetailParkir;
use App\Models\Member;
use App\Models\MetodePembayaran;
use App\Models\AreaKapasitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransaksiKeluarController extends Controller
{
    public function index()
    {
        try {
            $metodePembayaran = MetodePembayaran::all();
            
            return view('pages.parkir.keluar', [
                'metode' => $metodePembayaran
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat halaman: ' . $e->getMessage());
        }
    }

    public function cekTiket(Request $request)
    {
        Log::info('=== CEK TIKET ===');
        Log::info('Kode Tiket: ' . $request->kode_tiket);

        $request->validate([
            'kode_tiket' => 'required|string',
        ]);

        try {
            // CARI TRANSAKSI
            $transaksi = TransaksiParkir::with([
                'kendaraan.tipe',
                'kendaraan.pemilik',
                'areaParkir'
            ])
            ->where('kode_tiket', $request->kode_tiket)
            ->where('status', 'in')
            ->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan atau sudah keluar'
                ], 404);
            }

            Log::info('Transaksi found', [
                'id' => $transaksi->id_transaksi,
                'id_kendaraan' => $transaksi->id_kendaraan,
                'id_tipe' => $transaksi->kendaraan->id_tipe,
                'tipe_kendaraan' => $transaksi->kendaraan->tipe->tipe_kendaraan,
                'has_pemilik' => $transaksi->kendaraan->pemilik ? 'YES' : 'NO',
                'id_pemilik' => $transaksi->kendaraan->id_pemilik
            ]);

            // HITUNG DURASI
            $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
            $waktuKeluar = now();
            
            // Force absolute value
            $durasiMenit = abs($waktuKeluar->diffInMinutes($waktuMasuk, false));
            if ($durasiMenit < 1) { $durasiMenit = 1; }
            
            $durasiJamDesimal = $durasiMenit / 60;
            $durasiJamCeil = max(1, ceil($durasiJamDesimal));

            Log::info('Durasi calculated', [
                'menit' => $durasiMenit,
                'jam_desimal' => $durasiJamDesimal,
                'jam_ceil' => $durasiJamCeil
            ]);

            // ========================================
            // PERBAIKAN: QUERY LANGSUNG KE TARIF_PARKIR
            // Filter berdasarkan DURASI + TIPE KENDARAAN sekaligus!
            // ========================================
            
            $idTipe = $transaksi->kendaraan->id_tipe;
            
            Log::info('Searching tarif with duration AND tipe', [
                'durasi_jam_desimal' => $durasiJamDesimal,
                'durasi_formatted' => number_format($durasiJamDesimal, 2),
                'id_tipe' => $idTipe,
                'tipe_kendaraan' => $transaksi->kendaraan->tipe->tipe_kendaraan
            ]);

            // Query dengan JOIN dan filter tipe kendaraan
            $tarif = TarifParkir::join('detail_parkir', 'tarif_parkir.id_tarif_detail', '=', 'detail_parkir.id_tarif_detail')
                ->where('tarif_parkir.id_tipe', $idTipe)  // ← FILTER TIPE KENDARAAN!
                ->whereRaw('? BETWEEN detail_parkir.jam_min AND detail_parkir.jam_max', [$durasiJamDesimal])
                ->orderBy('detail_parkir.jam_min', 'asc')
                ->select('tarif_parkir.*', 'detail_parkir.jam_min', 'detail_parkir.jam_max')
                ->first();

            // Fallback dengan epsilon jika BETWEEN gagal
            if (!$tarif) {
                Log::warning('BETWEEN failed, trying with epsilon');
                
                $epsilon = 0.001;
                $tarif = TarifParkir::join('detail_parkir', 'tarif_parkir.id_tarif_detail', '=', 'detail_parkir.id_tarif_detail')
                    ->where('tarif_parkir.id_tipe', $idTipe)
                    ->where('detail_parkir.jam_min', '<=', $durasiJamDesimal + $epsilon)
                    ->where('detail_parkir.jam_max', '>=', $durasiJamDesimal - $epsilon)
                    ->orderBy('detail_parkir.jam_min', 'asc')
                    ->select('tarif_parkir.*', 'detail_parkir.jam_min', 'detail_parkir.jam_max')
                    ->first();
            }

            // Ultimate fallback: ambil range terkecil untuk tipe ini
            if (!$tarif) {
                Log::warning('No matching range, using smallest range for this tipe');
                
                $tarif = TarifParkir::join('detail_parkir', 'tarif_parkir.id_tarif_detail', '=', 'detail_parkir.id_tarif_detail')
                    ->where('tarif_parkir.id_tipe', $idTipe)
                    ->orderBy('detail_parkir.jam_min', 'asc')
                    ->select('tarif_parkir.*', 'detail_parkir.jam_min', 'detail_parkir.jam_max')
                    ->first();
            }

            if (!$tarif) {
                Log::error('No tarif found at all for this tipe!', [
                    'id_tipe' => $idTipe,
                    'tipe_kendaraan' => $transaksi->kendaraan->tipe->tipe_kendaraan
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tarif tidak ditemukan untuk tipe kendaraan ' . $transaksi->kendaraan->tipe->tipe_kendaraan
                ], 404);
            }

            Log::info('Tarif found', [
                'id_tarif' => $tarif->id_tarif,
                'id_tarif_detail' => $tarif->id_tarif_detail,
                'range' => $tarif->jam_min . '-' . $tarif->jam_max,
                'tarif' => $tarif->tarif
            ]);

            $totalTarif = $tarif->tarif;

            // CEK MEMBER DAN HITUNG DISKON
            $member = null;
            $diskon = 0;
            $persenDiskon = 0;
            $namaLevel = null;

            if ($transaksi->kendaraan->id_pemilik) {
                Log::info('Checking member for pemilik', [
                    'id_pemilik' => $transaksi->kendaraan->id_pemilik
                ]);

                $member = Member::with('level')
                    ->where('id_pemilik', $transaksi->kendaraan->id_pemilik)
                    ->where('status', 'aktif')
                    ->whereDate('berlaku_hingga', '>=', now())
                    ->first();

                if ($member) {
                    Log::info('Member found', [
                        'id_member' => $member->id_member,
                        'id_level' => $member->id_level
                    ]);

                    if ($member->level) {
                        $persenDiskon = $member->level->diskon_persen;
                        $diskon = $totalTarif * ($persenDiskon / 100);
                        $namaLevel = $member->level->nama_level;
                        
                        Log::info('Discount calculated', [
                            'level' => $namaLevel,
                            'persen' => $persenDiskon,
                            'diskon' => $diskon
                        ]);
                    }
                } else {
                    Log::info('No active member found for this pemilik');
                }
            } else {
                Log::info('Kendaraan tidak punya pemilik (id_pemilik is null)');
            }

            $totalBayar = $totalTarif - $diskon;

            // RETURN DATA
            return response()->json([
                'success' => true,
                'data' => [
                    'kode_tiket' => $transaksi->kode_tiket,
                    'plat_nomor' => $transaksi->kendaraan->plat_nomor,
                    'tipe_kendaraan' => $transaksi->kendaraan->tipe->tipe_kendaraan,
                    'pemilik' => $transaksi->kendaraan->pemilik->nama ?? 'Tidak Diketahui',
                    'area' => $transaksi->areaParkir->lokasi,
                    
                    'waktu_masuk' => $waktuMasuk->format('d/m/Y H:i:s'),
                    'waktu_keluar' => $waktuKeluar->format('d/m/Y H:i:s'),
                    'durasi_menit' => $durasiMenit,
                    'durasi_jam' => $durasiJamCeil,
                    
                    'tarif_parkir' => $tarif->tarif,
                    'total_tarif' => $totalTarif,
                    
                    'member' => $member ? [
                        'id_member' => $member->id_member,
                        'level' => $namaLevel,
                        'berlaku_hingga' => Carbon::parse($member->berlaku_hingga)->format('d/m/Y')
                    ] : null,
                    'persen_diskon' => $persenDiskon,
                    'diskon' => $diskon,
                    'total_bayar' => $totalBayar,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ERROR CEK TIKET: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function proses(Request $request)
    {
        Log::info('=== PROSES KELUAR ===');
        Log::info('Request', $request->all());

        $request->validate([
            'kode_tiket' => 'required|string',
            'id_metode' => 'required|exists:metode_pembayaran,id_metode',
        ]);

        DB::beginTransaction();

        try {
            // CARI TRANSAKSI
            $transaksi = TransaksiParkir::with([
                'kendaraan.tipe',
                'kendaraan.pemilik',
                'areaParkir'
            ])
            ->where('kode_tiket', $request->kode_tiket)
            ->where('status', 'in')
            ->lockForUpdate()
            ->first();

            if (!$transaksi) {
                DB::rollBack();
                return back()->with('error', 'Tiket tidak ditemukan');
            }

            // HITUNG DURASI
            $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
            $waktuKeluar = now();
            
            $durasiMenit = abs($waktuKeluar->diffInMinutes($waktuMasuk, false));
            if ($durasiMenit < 1) { $durasiMenit = 1; }
            
            $durasiJamDesimal = $durasiMenit / 60;
            $durasiJamCeil = max(1, ceil($durasiJamDesimal));

            // CARI TARIF (dengan tipe kendaraan)
            $idTipe = $transaksi->kendaraan->id_tipe;
            
            $tarif = TarifParkir::join('detail_parkir', 'tarif_parkir.id_tarif_detail', '=', 'detail_parkir.id_tarif_detail')
                ->where('tarif_parkir.id_tipe', $idTipe)
                ->whereRaw('? BETWEEN detail_parkir.jam_min AND detail_parkir.jam_max', [$durasiJamDesimal])
                ->orderBy('detail_parkir.jam_min', 'asc')
                ->select('tarif_parkir.*', 'detail_parkir.jam_min', 'detail_parkir.jam_max')
                ->first();

            if (!$tarif) {
                $epsilon = 0.001;
                $tarif = TarifParkir::join('detail_parkir', 'tarif_parkir.id_tarif_detail', '=', 'detail_parkir.id_tarif_detail')
                    ->where('tarif_parkir.id_tipe', $idTipe)
                    ->where('detail_parkir.jam_min', '<=', $durasiJamDesimal + $epsilon)
                    ->where('detail_parkir.jam_max', '>=', $durasiJamDesimal - $epsilon)
                    ->orderBy('detail_parkir.jam_min', 'asc')
                    ->select('tarif_parkir.*', 'detail_parkir.jam_min', 'detail_parkir.jam_max')
                    ->first();
            }

            if (!$tarif) {
                $tarif = TarifParkir::join('detail_parkir', 'tarif_parkir.id_tarif_detail', '=', 'detail_parkir.id_tarif_detail')
                    ->where('tarif_parkir.id_tipe', $idTipe)
                    ->orderBy('detail_parkir.jam_min', 'asc')
                    ->select('tarif_parkir.*', 'detail_parkir.jam_min', 'detail_parkir.jam_max')
                    ->first();
            }

            if (!$tarif) {
                DB::rollBack();
                return back()->with('error', 'Tarif tidak ditemukan untuk tipe kendaraan ini');
            }

            $totalTarif = $tarif->tarif;

            // CEK MEMBER & HITUNG DISKON
            $member = null;
            $diskon = 0;
            $persenDiskon = 0;

            if ($transaksi->kendaraan->id_pemilik) {
                $member = Member::with('level')
                    ->where('id_pemilik', $transaksi->kendaraan->id_pemilik)
                    ->where('status', 'aktif')
                    ->whereDate('berlaku_hingga', '>=', now())
                    ->first();

                if ($member && $member->level) {
                    $persenDiskon = $member->level->diskon_persen;
                    $diskon = $totalTarif * ($persenDiskon / 100);
                }
            }

            $totalBayar = $totalTarif - $diskon;

            Log::info('Calculation complete', [
                'total_tarif' => $totalTarif,
                'diskon' => $diskon,
                'total_bayar' => $totalBayar
            ]);

            // UPDATE TRANSAKSI
            $transaksi->update([
                'waktu_keluar' => $waktuKeluar,
                'durasi_jam' => $durasiJamCeil,
                'id_tarif' => $tarif->id_tarif,
                'id_member' => $member?->id_member,
                'id_metode' => $request->id_metode,
                'id_user' => null,
                'status' => 'out',
            ]);

            // KEMBALIKAN SLOT
            AreaKapasitas::where('id_area', $transaksi->id_area)
                ->where('id_tipe', $transaksi->kendaraan->id_tipe)
                ->increment('kapasitas');

            DB::commit();

            Log::info('=== PROSES KELUAR SUCCESS ===');

            // Buat object untuk detailParkir dari tarif yang sudah ada
            $detailParkir = (object)[
                'id_tarif_detail' => $tarif->id_tarif_detail,
                'jam_min' => $tarif->jam_min,
                'jam_max' => $tarif->jam_max
            ];

            // TAMPILKAN STRUK
            return view('pages.parkir.tiket-keluar', [
                'transaksi' => $transaksi->fresh([
                    'kendaraan.tipe', 
                    'kendaraan.pemilik', 
                    'areaParkir', 
                    'metodePembayaran',
                    'tarifParkir.detailParkir'
                ]),
                'tarif' => $tarif,
                'detailParkir' => $detailParkir,
                'member' => $member,
                'persen_diskon' => $persenDiskon,
                'diskon' => $diskon,
                'total_tarif' => $totalTarif,
                'total_bayar' => $totalBayar,
                'durasi_menit' => $durasiMenit,
                'durasi_jam' => $durasiJamCeil,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('ERROR PROSES KELUAR: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}