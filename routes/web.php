<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TipeKendaraanController;
use App\Http\Controllers\PemilikController;
use App\Http\Controllers\AreaParkirController;
use App\Http\Controllers\AreaKapasitasController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TarifParkirController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\DetailParkirController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransaksiMasukController;
use App\Http\Controllers\TransaksiKeluarController;
use App\Http\Controllers\BreakdownLaporanHarianController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\MasterDataController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES (Tanpa Middleware)
|--------------------------------------------------------------------------
*/

Route::middleware(['guest', 'nocache'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware(['auth', 'nocache'])
        ->name('logout');

/*
|--------------------------------------------------------------------------
| ROOT REDIRECT
|--------------------------------------------------------------------------
*/

// Redirect root ke dashboard (akan auto-redirect ke login jika belum auth)
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

/*
|--------------------------------------------------------------------------
| ROUTES DENGAN MIDDLEWARE AUTH
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'nocache'])->group(function () {
    
    // =========================================
    // DASHBOARD (SEMUA ROLE)
    // =========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password/update', [ChangePasswordController::class, 'update'])->name('password.update');

    
    // =========================================
    // MASER DATA (SEMUA ROLE)
    // =========================================

    // Route untuk Data Parkir
    Route::get('/master-data', [App\Http\Controllers\MasterDataController::class, 'parkir'])
        ->name('master-data.data-parkir')
        ->middleware('auth');

    // Riwayat Transaksi
    Route::get('/master-data/riwayat-transaksi', [App\Http\Controllers\MasterDataController::class, 'riwayatTransaksi'])
        ->name('master-data.riwayat-transaksi')
        ->middleware('auth');

    // Route untuk Data Member & Kendaraan
    Route::get('/master-data/member-kendaraan', [App\Http\Controllers\MasterDataController::class, 'memberKendaraan'])
        ->name('master-data.member-kendaraan')
        ->middleware('auth');

    // =========================================
    // ACTIVITY LOG (Admin & Owner)
    // =========================================
    Route::middleware(['check.role:admin,owner'])->group(function () {
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('/activity-log/{id}', [ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::get('/activity-log-export', [ActivityLogController::class, 'export'])->name('activity-log.export');
    });
    
    // =========================================
    // USER MANAGEMENT (Admin)
    // =========================================
    Route::middleware(['check.role:admin'])->group(function () {
        Route::resource('user', UserController::class);
        Route::get('user/{user}/password', [UserController::class, 'editPassword'])->name('user.password.edit');
        Route::put('user/{user}/password', [UserController::class, 'updatePassword'])->name('user.password.update');
        Route::get('/user-trash', [UserController::class, 'trash'])->name('user.trash');
        Route::post('/user/{id}/restore', [UserController::class, 'restore'])->name('user.restore');
        
        // ROLE
        Route::resource('roles', RoleController::class);
        Route::get('/roles-trash', [RoleController::class, 'trash'])->name('roles.trash');
        Route::post('/roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
    });
    
    // =========================================
    // MASTER DATA CRUD (Admin)
    // =========================================
    Route::middleware(['check.role:admin'])->group(function () {
        
        // TIPE KENDARAAN
        Route::resource('tipe-kendaraan', TipeKendaraanController::class);
        
        // PEMILIK
        Route::resource('pemilik', PemilikController::class);
        Route::get('/pemilik-trash', [PemilikController::class, 'trash'])->name('pemilik.trash');
        Route::post('/pemilik/{id}/restore', [PemilikController::class, 'restore'])->name('pemilik.restore');
        
        // AREA PARKIR
        Route::resource('area-parkir', AreaParkirController::class);
        Route::get('/area-parkir-trash', [AreaParkirController::class, 'trash'])->name('area-parkir.trash');
        Route::post('/area-parkir/{id}/restore', [AreaParkirController::class, 'restore'])->name('area-parkir.restore');
        
        // KAPASITAS PARKIR
        Route::resource('area-kapasitas', AreaKapasitasController::class);

        // DETAIL DURASI PARKIR
        Route::resource('detail-parkir', DetailParkirController::class);
        Route::get('detail-parkir-trash', [DetailParkirController::class, 'trash'])
            ->name('detail-parkir.trash');
        Route::post('detail-parkir/{id}/restore', [DetailParkirController::class, 'restore'])
            ->name('detail-parkir.restore');
        
        // KENDARAAN
        Route::resource('data-kendaraan', KendaraanController::class);
        Route::get('/data-kendaraan-trash', [KendaraanController::class, 'trash'])->name('data-kendaraan.trash');
        Route::post('/data-kendaraan/{id}/restore', [KendaraanController::class, 'restore'])->name('data-kendaraan.restore');
        
        // MEMBER
        Route::resource('member', MemberController::class);
        Route::get('/member-trash', [MemberController::class, 'trash'])->name('member.trash');
        Route::post('/member/{id}/restore', [MemberController::class, 'restore'])->name('member.restore');
        
        // TARIF PARKIR
        Route::resource('tarif-parkir', TarifParkirController::class);
        Route::get('/tarif-parkir-trash', [TarifParkirController::class, 'trash'])->name('tarif-parkir.trash');
        Route::post('/tarif-parkir/{id}/restore', [TarifParkirController::class, 'restore'])->name('tarif-parkir.restore');
        
        // METODE PEMBAYARAN
        Route::resource('metode-pembayaran', MetodePembayaranController::class);
        Route::get('/metode-pembayaran-trash', [MetodePembayaranController::class, 'trash'])->name('metode-pembayaran.trash');
        Route::post('/metode-pembayaran/{id}/restore', [MetodePembayaranController::class, 'restore'])->name('metode-pembayaran.restore');
    });
    
    // =========================================
    // TRANSAKSI (Petugas)
    // =========================================
    Route::middleware(['check.role:petugasparkir,petugas'])->group(function () {
        
        // PARKIR MASUK
        Route::get('/parkir/masuk', [TransaksiMasukController::class, 'index'])->name('parkir.masuk');
        Route::post('/parkir/masuk', [TransaksiMasukController::class, 'store'])->name('parkir.masuk.store');
        Route::get('/parkir/autocomplete-plat', [TransaksiMasukController::class, 'autocompletePlat'])->name('parkir.masuk.autocomplete.plat');
        Route::post('/parkir/tiket-masuk/cetak', [TransaksiMasukController::class, 'cetakTiket'])->name('parkir.tiket-masuk.cetak');
    
        
        // PARKIR KELUAR
        Route::get('/parkir/keluar', [TransaksiKeluarController::class, 'index'])->name('parkir.keluar');
        Route::post('/parkir/keluar/cek', [TransaksiKeluarController::class, 'cekTiket'])->name('parkir.keluar.cek');
        Route::post('/parkir/keluar/proses', [TransaksiKeluarController::class, 'proses'])->name('parkir.keluar.proses');
    });

    // =========================================
    // BREAKDOWN LAPORAN HARIAN (Owner, Petugas)
    // =========================================
    Route::get('/laporan', [BreakdownLaporanHarianController::class, 'breakdown'])
        ->name('laporan.breakdown')
        ->middleware('auth');
    
    // Export CSV (Admin only)
    Route::get('/laporan/export', [BreakdownLaporanHarianController::class, 'export'])
        ->name('laporan.export')
        ->middleware('check.role:admin');
    
});

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTE
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    abort(404);
});