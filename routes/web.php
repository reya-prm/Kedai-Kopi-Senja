<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

// buka alamat '/' langsung dilempar ke dashboard
Route::redirect('/', '/dashboard');

// buat yg belum login nanti di lempar ke login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// buat yg udah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // rute uji sementara
    Route::middleware('role:manajer')->get('/uji/manajer', [DashboardController::class, 'testManajer'])->name('test.manajer');
    Route::middleware('role:kasir')->get('/uji/kasir', [DashboardController::class, 'testKasir'])->name('test.kasir');
});