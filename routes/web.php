<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TipeKendaraanController;
use App\Http\Controllers\PemilikController;
use App\Http\Controllers\AreaParkirController;
use App\Http\Controllers\AreaKapasitasController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TarifParkirController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransaksiMasukController;
use App\Http\Controllers\TransaksiKeluarController;

Route::get('/', function () {
    return view('app');
});

//----- USER -----//
Route::resource('user', UserController::class);
Route::get('user/{user}/password', [UserController::class, 'editPassword'])->name('user.password.edit');
Route::put('user/{user}/password', [UserController::class, 'updatePassword'])->name('user.password.update');
Route::get('/user-trash', [UserController::class, 'trash'])
->name('user.trash');
Route::post('/user/{id}/restore', [UserController::class, 'restore'])
->name('user.restore');

//----- TIPE KENDARAAN -----//
Route::resource('tipe-kendaraan', TipeKendaraanController::class);

//----- PEMILIK ------//
Route::resource('pemilik', PemilikController::class);
Route::get('/pemilik-trash', [PemilikController::class, 'trash'])
    ->name('pemilik.trash');
Route::post('/pemilik/{id}/restore', [PemilikController::class, 'restore'])
    ->name('pemilik.restore');

//----- AREA PARKIR -----//
Route::resource('area-parkir', AreaParkirController::class);
Route::get('/area-parkir-trash', [AreaParkirController::class, 'trash'])
    ->name('area-parkir.trash');
Route::post('/area-parkir/{id}/restore', [AreaParkirController::class, 'restore'])
    ->name('area-parkir.restore');

//----- KAPASITAS PARKIR -----//
Route::resource('area-kapasitas', AreaKapasitasController::class);

//----- KENDARAAN -----//
Route::resource('data-kendaraan', KendaraanController::class);
Route::get('/data-kendaraan-trash', [KendaraanController::class, 'trash'])
    ->name('data-kendaraan.trash');
Route::post('/data-kendaraan/{id}/restore', [KendaraanController::class, 'restore'])
    ->name('data-kendaraan.restore');

//----- MEMBER -----//
Route::resource('member', MemberController::class);
Route::get('/member-trash', [MemberController::class, 'trash'])
    ->name('member.trash');
Route::post('/member/{id}/restore', [MemberController::class, 'restore'])
    ->name('member.restore');

//----- TARIF PARKIR -----//
Route::resource('tarif-parkir', TarifParkirController::class);
Route::get('/tarif-parkir-trash', [TarifParkirController::class, 'trash'])
    ->name('tarif-parkir.trash');
Route::post('/tarif-parkir/{id}/restore', [TarifParkirController::class, 'restore'])
    ->name('tarif-parkir.restore');

//----- METODE PEMBAYARAN -----//
Route::resource('metode-pembayaran', MetodePembayaranController::class);
Route::get('/metode-pembayaran-trash', [MetodePembayaranController::class, 'trash'])
    ->name('metode-pembayaran.trash');
Route::post('/metode-pembayaran/{id}/restore', [MetodePembayaranController::class, 'restore'])
    ->name('metode-pembayaran.restore');

//----- ROLE USER -----//
Route::resource('roles', RoleController::class);
Route::get('/roles-trash', [RoleController::class, 'trash'])
->name('roles.trash');
Route::post('/roles/{id}/restore', [RoleController::class, 'restore'])
->name('roles.restore');

//------- SCAN QR KODE MASUK -------//

Route::get('/parkir/masuk', [TransaksiMasukController::class, 'index'])
    ->name('parkir.masuk');
    //->middleware('auth');

// Proses simpan transaksi masuk dan generate tiket
Route::post('/parkir/masuk', [TransaksiMasukController::class, 'store'])
    ->name('parkir.masuk.store');
    //->middleware('auth');

Route::get('/parkir/autocomplete-plat', [TransaksiMasukController::class, 'autocompletePlat'])
    ->name('parkir.masuk.autocomplete.plat');

//------ SCAN QR KODE KELUAR -------//

// Halaman scan QR Code / input manual
Route::get('/parkir/keluar', [TransaksiKeluarController::class, 'index'])
    ->name('parkir.keluar');

// API untuk cek tiket (AJAX) - dipanggil saat scan QR
Route::post('/parkir/keluar/cek', [TransaksiKeluarController::class, 'cekTiket'])
    ->name('parkir.keluar.cek');

// Proses keluar dan cetak struk
Route::post('/parkir/keluar/proses', [TransaksiKeluarController::class, 'proses'])
    ->name('parkir.keluar.proses');