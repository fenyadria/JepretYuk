<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaketFotoController;
use App\Http\Controllers\PesananController;

/*
|--------------------------------------------------------------------------
| Web Routes - JepretYuk (Sistem Pesan Fotografer)
|--------------------------------------------------------------------------
|
| Route dikelompokkan berdasarkan:
| 1. Auth (Login, Register, Logout) - Terbuka untuk umum
| 2. Fotografer - Dilindungi middleware 'auth' + 'role.fotografer'
| 3. Klien - Dilindungi middleware 'auth' + 'role.klien'
| 4. Shared - Route yang bisa diakses oleh kedua role
|
*/

// ===================================================================
// HALAMAN UTAMA
// ===================================================================
Route::get('/', function () {
    return view('welcome');
});

// ===================================================================
// ROUTE AUTH (Login, Register, Logout)
// ===================================================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===================================================================
// ROUTE FOTOGRAFER (Admin) - Kelola Paket Foto & Lihat Semua Pesanan
// ===================================================================
Route::middleware(['auth', 'role.fotografer'])->prefix('fotografer')->group(function () {

    // CRUD Paket Foto (hanya fotografer yang bisa kelola)
    Route::get('/paket', [PaketFotoController::class, 'index'])->name('paket.index');
    Route::get('/paket/create', [PaketFotoController::class, 'create'])->name('paket.create');
    Route::post('/paket', [PaketFotoController::class, 'store'])->name('paket.store');
    Route::get('/paket/{paket}/edit', [PaketFotoController::class, 'edit'])->name('paket.edit');
    Route::put('/paket/{paket}', [PaketFotoController::class, 'update'])->name('paket.update');
    Route::delete('/paket/{paket}', [PaketFotoController::class, 'destroy'])->name('paket.destroy');

    // Fotografer melihat & mengelola semua pesanan (ubah status)
    Route::get('/pesanan', [PesananController::class, 'index'])->name('fotografer.pesanan.index');
    Route::get('/pesanan/{pesanan}/edit', [PesananController::class, 'edit'])->name('fotografer.pesanan.edit');
    Route::put('/pesanan/{pesanan}', [PesananController::class, 'update'])->name('fotografer.pesanan.update');
    Route::delete('/pesanan/{pesanan}', [PesananController::class, 'destroy'])->name('fotografer.pesanan.destroy');
});

// ===================================================================
// ROUTE KLIEN (Pelanggan) - Buat & Kelola Pesanan Sendiri
// ===================================================================
Route::middleware(['auth', 'role.klien'])->prefix('klien')->group(function () {

    // Klien membuat dan mengelola pesanan miliknya
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/{pesanan}/edit', [PesananController::class, 'edit'])->name('pesanan.edit');
    Route::put('/pesanan/{pesanan}', [PesananController::class, 'update'])->name('pesanan.update');
    Route::delete('/pesanan/{pesanan}', [PesananController::class, 'destroy'])->name('pesanan.destroy');
});
