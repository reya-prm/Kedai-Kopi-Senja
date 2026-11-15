<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/dashboard');

//buat yang belum login
Route::middleware('guest')->group(function (){
    Route::get('/login', [LoginController::class, 'form'])->name('login');
    Route::post('/login', [LoginController::class, 'masuk']);
});

//buat yg udah login
Route::middleware('auth')->group(function (){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/keluar', [LoginController::class, 'keluar'])->name('keluar');

    //khusus manajer
    Route::middleware('peran:manajer')->group(function (){
        Route::get('/manajer/uji', [DashboardController::class, 'ujimanajer'])->name('manajer.uji');
    });

    //khusus kasir
    Route::middleware('peran:kasir')->group(function (){
        Route::get('/kasir/uji', [DashboardController::class, 'ujikasir'])->name('kasir.uji');
    });
});

