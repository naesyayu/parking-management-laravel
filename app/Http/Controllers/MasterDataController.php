<?php

namespace App\Http\Controllers;

use App\Models\TipeKendaraan;
use App\Models\AreaParkir;
use App\Models\TarifParkir;
use App\Models\DetailParkir;
use App\Models\Pemilik;
use App\Models\MemberLevel;
use App\Models\TransaksiParkir;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // DATA PARKIR
    public function parkir()
    {
        $tipeKendaraan = TipeKendaraan::with([
            'areaKapasitas.area',
            'tarifParkir.detailParkir'
        ])->get();

        $areaParkir = AreaParkir::with('kapasitas.tipe')->get();

        $detailParkir = DetailParkir::orderBy('jam_min')->get();

        return view('pages.master-data.data-parkir', compact('tipeKendaraan', 'areaParkir', 'detailParkir'));
    }

    // RIWAYAT TRANSAKSI
    public function riwayatTransaksi(Request $request)
    {
        // Query dasar
        $query = TransaksiParkir::with([
            'kendaraan.tipe',
            'areaParkir'
        ])->where('status', 'out');

        // Filter tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('waktu_keluar', [
                $request->tanggal_dari . ' 00:00:00',
                $request->tanggal_sampai . ' 23:59:59'
            ]);
        }

        // Filter plat nomor
        if ($request->filled('plat_nomor')) {
            $query->whereHas('kendaraan', function($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%');
            });
        }

        // Filter area
        if ($request->filled('id_area')) {
            $query->where('id_area', $request->id_area);
        }

        // Ambil data dengan pagination
        $transaksi = $query->orderBy('waktu_keluar', 'desc')->paginate(20);

        // Data untuk dropdown
        $areaParkir = AreaParkir::all();

        return view('pages.master-data.riwayat-transaksi', compact('transaksi', 'areaParkir'));
    }

    // DATA MEMBER & KENDARAAN
    public function memberKendaraan(Request $request)
    {
        // Query untuk Pemilik yang MEMILIKI Member
        $queryMember = Pemilik::with([
            'members.level',
            'kendaraan.tipe'
        ])->whereHas('members');

        // Query untuk Pemilik yang TIDAK MEMILIKI Member
        $queryNonMember = Pemilik::with([
            'kendaraan.tipe'
        ])->doesntHave('members');

        // Query untuk Kendaraan TIDAK MEMILIKI Pemilik
        $queryKendaraanTanpaPemilik = Kendaraan::with('tipe')
            ->whereNull('id_pemilik');

        // Filter Nama Pemilik (tidak berlaku untuk kendaraan tanpa pemilik)
        if ($request->filled('nama')) {
            $queryMember->where('nama', 'like', '%' . $request->nama . '%');
            $queryNonMember->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter No HP (tidak berlaku untuk kendaraan tanpa pemilik)
        if ($request->filled('no_hp')) {
            $queryMember->where('no_hp', 'like', '%' . $request->no_hp . '%');
            $queryNonMember->where('no_hp', 'like', '%' . $request->no_hp . '%');
        }

        // Filter Plat Nomor
        if ($request->filled('plat_nomor')) {
            $queryMember->whereHas('kendaraan', function($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%');
            });
            $queryNonMember->whereHas('kendaraan', function($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%');
            });
            $queryKendaraanTanpaPemilik->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%');
        }

        // Filter Level Member (hanya untuk yang punya member)
        if ($request->filled('id_level')) {
            $queryMember->whereHas('members', function($q) use ($request) {
                $q->where('id_level', $request->id_level);
            });
        }

        // Filter Status Member
        if ($request->filled('status_member')) {
            $queryMember->whereHas('members', function($q) use ($request) {
                $q->where('status', $request->status_member);
            });
        }

        // Ambil data dengan pagination - PENTING: Gunakan pageName berbeda untuk tiap tab
        $pemilikMember = $queryMember->paginate(10, ['*'], 'member_page');
        $pemilikNonMember = $queryNonMember->paginate(10, ['*'], 'non_member_page');
        $kendaraanTanpaPemilik = $queryKendaraanTanpaPemilik->paginate(10, ['*'], 'kendaraan_page');

        // Data untuk filter dropdown
        $memberLevels = MemberLevel::all();

        // FIX: Deteksi active tab berdasarkan pagination query
        $activeTab = 'member'; // default
        if ($request->has('non_member_page')) {
            $activeTab = 'non-member';
        } elseif ($request->has('kendaraan_page')) {
            $activeTab = 'kendaraan';
        }

        return view('pages.master-data.data-member-kendaraan', compact(
            'pemilikMember',
            'pemilikNonMember',
            'kendaraanTanpaPemilik',
            'memberLevels',
            'activeTab' // PASS active tab ke view
        ));
    }
}