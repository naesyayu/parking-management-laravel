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

Route::resource('user', UserController::class);
Route::get('user/{user}/password', [UserController::class, 'editPassword'])->name('user.password.edit');
Route::put('user/{user}/password', [UserController::class, 'updatePassword'])->name('user.password.update');

Route::resource('tipe-kendaraan', TipeKendaraanController::class);

Route::resource('pemilik', PemilikController::class);
// halaman trash
Route::get('/pemilik-trash', [PemilikController::class, 'trash'])
    ->name('pemilik.trash');
// restore data
Route::post('/pemilik/{id}/restore', [PemilikController::class, 'restore'])
    ->name('pemilik.restore');

Route::resource('area-parkir', AreaParkirController::class);

Route::resource('area-kapasitas', AreaKapasitasController::class);

Route::resource('data-kendaraan', KendaraanController::class);

Route::resource('member', MemberController::class);

Route::resource('tarif-parkir', TarifParkirController::class);

Route::resource('metode-pembayaran', MetodePembayaranController::class);

Route::resource('roles', RoleController::class);

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