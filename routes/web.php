<?php

use App\Http\Controllers\Manajer\BahanController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// buat yang BELUM login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// buat yang SUDAH login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // khusus manajer. prefix = awalan URL (/manajer/...), name = awalan nama rute (manajer....)
    Route::middleware('role:manajer')->prefix('manajer')->name('manajer.')->group(function () {

        // ---- bahan ----
        Route::get('/bahan', [BahanController::class, 'index'])->name('bahan.index');
        Route::get('/bahan/create', [BahanController::class, 'create'])->name('bahan.create');
        Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
        // {bahan} = id di URL, Laravel otomatis nyari datanya dan ngirim ke $bahan di controller
        Route::get('/bahan/{bahan}/edit', [BahanController::class, 'edit'])->name('bahan.edit');
        Route::put('/bahan/{bahan}', [BahanController::class, 'update'])->name('bahan.update');
        Route::delete('/bahan/{bahan}', [BahanController::class, 'destroy'])->name('bahan.destroy');
    });

    // rute uji sementara dari tahap login, dihapus nanti
    Route::middleware('role:manajer')->get('/uji/manajer', [DashboardController::class, 'testManajer'])->name('test.manajer');
    Route::middleware('role:kasir')->get('/uji/kasir', [DashboardController::class, 'testKasir'])->name('test.kasir');
});