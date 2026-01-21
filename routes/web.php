<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TipeKendaraanController;

Route::get('/', function () {
    return view('app');
});

Route::resource('user', UserController::class);
Route::get('user/{user}/password', [UserController::class, 'editPassword'])->name('user.password.edit');
Route::put('user/{user}/password', [UserController::class, 'updatePassword'])->name('user.password.update');

Route::resource('tipe-kendaraan', TipeKendaraanController::class);
