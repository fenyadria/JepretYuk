<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaketFotoController;
use App\Http\Controllers\PesananController;


Route::get('/', function () {
    return view('welcome');
});


Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Katalog Paket Foto (Dapat diakses oleh klien & publik)
Route::get('/katalog', [PaketFotoController::class, 'katalog'])->name('paket.katalog');


Route::middleware(['auth', 'role.fotografer'])->prefix('fotografer')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'fotografer'])->name('fotografer.dashboard');

    Route::get('/paket', [PaketFotoController::class, 'index'])->name('paket.index');
    Route::get('/paket/create', [PaketFotoController::class, 'create'])->name('paket.create');
    Route::post('/paket', [PaketFotoController::class, 'store'])->name('paket.store');
    Route::get('/paket/{paket}/edit', [PaketFotoController::class, 'edit'])->name('paket.edit');
    Route::put('/paket/{paket}', [PaketFotoController::class, 'update'])->name('paket.update');
    Route::delete('/paket/{paket}', [PaketFotoController::class, 'destroy'])->name('paket.destroy');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('fotografer.pesanan.index');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('fotografer.pesanan.show');
    Route::get('/pesanan/{pesanan}/edit', [PesananController::class, 'edit'])->name('fotografer.pesanan.edit');
    Route::put('/pesanan/{pesanan}', [PesananController::class, 'update'])->name('fotografer.pesanan.update');
    Route::delete('/pesanan/{pesanan}', [PesananController::class, 'destroy'])->name('fotografer.pesanan.destroy');

    // Kelola Tim Fotografer / Admin
    Route::get('/admin-team', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('fotografer.admin.index');
    Route::get('/admin-team/create', [\App\Http\Controllers\AdminUserController::class, 'create'])->name('fotografer.admin.create');
    Route::post('/admin-team', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('fotografer.admin.store');
    Route::delete('/admin-team/{user}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('fotografer.admin.destroy');
});


Route::middleware(['auth', 'role.klien'])->prefix('klien')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'klien'])->name('klien.dashboard');
    Route::get('/paket', [PaketFotoController::class, 'katalog'])->name('klien.paket.index');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{pesanan}/edit', [PesananController::class, 'edit'])->name('pesanan.edit');
    Route::put('/pesanan/{pesanan}', [PesananController::class, 'update'])->name('pesanan.update');
    Route::delete('/pesanan/{pesanan}', [PesananController::class, 'destroy'])->name('pesanan.destroy');
    Route::post('/pesanan/{pesanan}/upload-bukti', [PesananController::class, 'uploadBukti'])->name('pesanan.uploadBukti');
});
