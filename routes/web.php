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

Route::get('/', function () {
    return view('app');
});

Route::resource('user', UserController::class);
Route::get('user/{user}/password', [UserController::class, 'editPassword'])->name('user.password.edit');
Route::put('user/{user}/password', [UserController::class, 'updatePassword'])->name('user.password.update');

Route::resource('tipe-kendaraan', TipeKendaraanController::class);

Route::resource('pemilik', PemilikController::class);

Route::resource('area-parkir', AreaParkirController::class);

Route::resource('area-kapasitas', AreaKapasitasController::class);

Route::resource('data-kendaraan', KendaraanController::class);

Route::resource('member', MemberController::class);

Route::resource('tarif-parkir', TarifParkirController::class);

Route::resource('metode-pembayaran', MetodePembayaranController::class);