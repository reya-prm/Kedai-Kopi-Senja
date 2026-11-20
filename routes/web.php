<?php

use App\Http\Controllers\Kasir\CustomerController;
use App\Http\Controllers\Kasir\MenuListController;
use App\Http\Controllers\Kasir\OrderController;
use App\Http\Controllers\Manajer\BahanController;
use App\Http\Controllers\Manajer\LaporanController;
use App\Http\Controllers\Manajer\MenuController;
use App\Http\Controllers\Manajer\StokMasukController;
use App\Http\Controllers\Manajer\SupplierController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\PasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::guest()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'manajer' => redirect()->route('manajer.bahan.index'),
        'kasir' => redirect()->route('kasir.menu.index'),
        default => abort(403),
    };
})->name('home');

// buat yang BELUM login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// buat yang SUDAH login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // khusus manajer
    Route::middleware('role:manajer')->prefix('manajer')->name('manajer.')->group(function () {

        // bahan
        Route::get('/bahan', [BahanController::class, 'index'])->name('bahan.index');
        Route::get('/bahan/create', [BahanController::class, 'create'])->name('bahan.create');
        Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
        Route::get('/bahan/{bahan}/edit', [BahanController::class, 'edit'])->name('bahan.edit');
        Route::put('/bahan/{bahan}', [BahanController::class, 'update'])->name('bahan.update');
        // bahan menipis
        Route::get('/bahan-menipis', [BahanController::class, 'lowStock'])->name('bahan.low-stock');

        // menu
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::patch('/menu/{menu}/toggle', [MenuController::class, 'toggleActive'])->name('menu.toggle');

        // resep (diatur dari halaman ubah menu)
        Route::post('/menu/{menu}/recipe', [MenuController::class, 'storeRecipe'])->name('menu.recipe.store');
        Route::delete('/menu/{menu}/recipe/{resep}', [MenuController::class, 'destroyRecipe'])->name('menu.recipe.destroy');
        // ringkasan resep semua menu + hapus seluruh resep satu menu
        Route::get('/resep', [MenuController::class, 'recipeIndex'])->name('resep.index');
        Route::delete('/menu/{menu}/recipe', [MenuController::class, 'clearRecipe'])->name('menu.recipe.clear');

        // stok masuk
        Route::get('/stok-masuk', [StokMasukController::class, 'index'])->name('stok.index');
        Route::post('/stok-masuk', [StokMasukController::class, 'store'])->name('stok.store');

        // laporan terpadu (rute lama tetap diarahkan ke laporan yang sama)
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/margin', [LaporanController::class, 'index'])->name('laporan.margin');
        Route::get('/laporan/penjualan', [LaporanController::class, 'index'])->name('laporan.penjualan');
        Route::get('/laporan/penjualan-menu', [LaporanController::class, 'index'])->name('report.sales');

        // sinkron harga supplier
        Route::post('/supplier/sync', [SupplierController::class, 'sync'])->name('supplier.sync');
    });

    // khusus kasir
    Route::middleware('role:kasir')->prefix('kasir')->name('kasir.')->group(function () {

        // menu
        Route::get('/menu', [MenuListController::class, 'index'])->name('menu.index');

        // pesanan
        Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/buat', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/pesanan', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/pesanan/{pesanan}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/pesanan/{pesanan}/struk', [OrderController::class, 'receipt'])->name('orders.receipt');
        Route::patch('/pesanan/{pesanan}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::patch('/pesanan/{pesanan}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');

        // ganti password
        Route::get('/pelanggan', [CustomerController::class, 'index'])->name('customers.index');
        Route::patch('/pelanggan/{user}/reset-password', [CustomerController::class, 'resetPassword'])->name('customers.reset');
    });
});
