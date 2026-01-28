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
                'kendaraan.pemilik', // PENTING: Load pemilik dulu
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
                'has_pemilik' => $transaksi->kendaraan->pemilik ? 'YES' : 'NO',
                'id_pemilik' => $transaksi->kendaraan->id_pemilik
            ]);

            // HITUNG DURASI
            $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
            $waktuKeluar = now();
            
            // PERBAIKAN: Force absolute value untuk handle timezone issues
            $durasiMenit = abs($waktuKeluar->diffInMinutes($waktuMasuk, false));
            
            // Fallback: minimal 1 menit
            if ($durasiMenit < 1) {
                $durasiMenit = 1;
            }
            
            // PERBAIKAN: Konversi ke jam desimal
            $durasiJamDesimal = $durasiMenit / 60;
            
            // Untuk tampilan (pembulatan ke atas, minimal 1 jam)
            $durasiJamCeil = max(1, ceil($durasiJamDesimal));

            Log::info('Durasi calculated', [
                'menit' => $durasiMenit,
                'jam_desimal' => $durasiJamDesimal,
                'jam_ceil' => $durasiJamCeil
            ]);

            // CARI TARIF YANG SESUAI
            Log::info('Searching tarif with duration', [
                'durasi_jam_desimal' => $durasiJamDesimal,
                'durasi_formatted' => number_format($durasiJamDesimal, 2),
                'id_tipe' => $transaksi->kendaraan->id_tipe
            ]);

            // Debug: tampilkan semua detail parkir
            $allDetails = DetailParkir::orderBy('jam_min')->get();
            Log::info('All detail_parkir in DB:', $allDetails->toArray());

            // PERBAIKAN: Gunakan BETWEEN untuk lebih akurat
            $detailParkir = DetailParkir::whereRaw('? BETWEEN jam_min AND jam_max', [$durasiJamDesimal])
                ->orderBy('jam_min', 'asc')
                ->first();

            Log::info('DetailParkir query result (BETWEEN)', [
                'found' => $detailParkir ? 'YES' : 'NO',
                'query' => "{$durasiJamDesimal} BETWEEN jam_min AND jam_max"
            ]);

            // Jika tidak ketemu dengan BETWEEN, coba dengan <= dan >=
            if (!$detailParkir) {
                Log::warning('BETWEEN failed, trying <= and >= with epsilon');
                
                $epsilon = 0.001; // Toleransi untuk floating point
                $detailParkir = DetailParkir::where('jam_min', '<=', $durasiJamDesimal + $epsilon)
                    ->where('jam_max', '>=', $durasiJamDesimal - $epsilon)
                    ->orderBy('jam_min', 'asc')
                    ->first();
            }

            // Jika masih tidak ketemu, ambil range TERKECIL sebagai fallback
            if (!$detailParkir) {
                Log::warning('No matching range found! Using fallback (SMALLEST range)');
                $detailParkir = DetailParkir::orderBy('jam_min', 'asc')->first();
            }

            if (!$detailParkir) {
                Log::error('No detail_parkir found in database at all!');
                return response()->json([
                    'success' => false,
                    'message' => 'Data tarif tidak ditemukan di database'
                ], 404);
            }

            Log::info('Detail parkir found', [
                'id' => $detailParkir->id_tarif_detail,
                'range' => $detailParkir->jam_min . '-' . $detailParkir->jam_max
            ]);

            // Cari tarif berdasarkan detail dan tipe kendaraan
            Log::info('Searching tarif_parkir', [
                'id_tarif_detail' => $detailParkir->id_tarif_detail,
                'id_tipe' => $transaksi->kendaraan->id_tipe
            ]);

            // Debug: tampilkan semua tarif untuk tipe ini
            $allTarif = TarifParkir::where('id_tipe', $transaksi->kendaraan->id_tipe)->get();
            Log::info('All tarif for this tipe:', $allTarif->toArray());

            $tarif = TarifParkir::where('id_tarif_detail', $detailParkir->id_tarif_detail)
                ->where('id_tipe', $transaksi->kendaraan->id_tipe)
                ->first();

            if (!$tarif) {
                Log::error('Tarif not found!', [
                    'id_tarif_detail' => $detailParkir->id_tarif_detail,
                    'id_tipe' => $transaksi->kendaraan->id_tipe
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tarif tidak ditemukan untuk tipe kendaraan ini'
                ], 404);
            }

            $totalTarif = $tarif->tarif;

            Log::info('Tarif found', [
                'id_tarif' => $tarif->id_tarif,
                'tarif' => $totalTarif
            ]);

            // CEK MEMBER DAN HITUNG DISKON
            $member = null;
            $diskon = 0;
            $persenDiskon = 0;
            $namaLevel = null;

            // PERBAIKAN: Cek apakah kendaraan punya pemilik
            if ($transaksi->kendaraan->id_pemilik) {
                Log::info('Checking member for pemilik', [
                    'id_pemilik' => $transaksi->kendaraan->id_pemilik
                ]);

                // Query member secara eksplisit
                $member = Member::with('level')
                    ->where('id_pemilik', $transaksi->kendaraan->id_pemilik)
                    ->where('status', 'aktif')
                    ->whereDate('berlaku_hingga', '>=', now())  // ← Gunakan whereDate
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
            
            // PERBAIKAN: Force absolute value
            $durasiMenit = abs($waktuKeluar->diffInMinutes($waktuMasuk, false));
            if ($durasiMenit < 1) { $durasiMenit = 1; }
            
            $durasiJamDesimal = $durasiMenit / 60;
            $durasiJamCeil = max(1, ceil($durasiJamDesimal));

            // CARI TARIF
            $detailParkir = DetailParkir::whereRaw('? BETWEEN jam_min AND jam_max', [$durasiJamDesimal])
                ->orderBy('jam_min', 'asc')
                ->first();

            if (!$detailParkir) {
                $epsilon = 0.001;
                $detailParkir = DetailParkir::where('jam_min', '<=', $durasiJamDesimal + $epsilon)
                    ->where('jam_max', '>=', $durasiJamDesimal - $epsilon)
                    ->orderBy('jam_min', 'asc')
                    ->first();
            }

            if (!$detailParkir) {
                $detailParkir = DetailParkir::orderBy('jam_min', 'asc')->first();  // ← Ambil yang terkecil
            }

            $tarif = TarifParkir::where('id_tarif_detail', $detailParkir->id_tarif_detail)
                ->where('id_tipe', $transaksi->kendaraan->id_tipe)
                ->first();

            if (!$tarif) {
                DB::rollBack();
                return back()->with('error', 'Tarif tidak ditemukan');
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
                    ->whereDate('berlaku_hingga', '>=', now())  // ← Gunakan whereDate
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