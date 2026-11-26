<?php

use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

// Halaman Utama (Pilih Peran)
Route::get('/', fn() => view('welcome'))->name('home');

// Frontend: Pelanggan & Kasir
Route::get('/kasir', [PosController::class, 'index'])->name('pos.index');

// Backend: Khusus Pemilik Toko (Owner)
Route::get('/owner', [PosController::class, 'ownerIndex'])->name('owner.index');
Route::get('/owner/export/{type}', [PosController::class, 'exportExcel'])
    ->whereIn('type', ['penjualan', 'kulakan', 'omset'])
    ->name('owner.export');

Route::prefix('api/pos')->group(function () {
    Route::get('/all', [PosController::class, 'getAll']);
    Route::post('/login', [PosController::class, 'login']);
    Route::post('/logout', [PosController::class, 'logout']);
    Route::post('/produk', [PosController::class, 'addProduk']);
    Route::post('/produk/{id}', [PosController::class, 'updateProduk']);
    Route::delete('/produk/{id}', [PosController::class, 'deleteProduk']);
    Route::post('/penjualan', [PosController::class, 'addPenjualan']);
    Route::post('/absensi', [PosController::class, 'addAbsensi']);
});
