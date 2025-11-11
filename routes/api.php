<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;

// API untuk mencari produk
Route::get('/produk/{barcode}', [ProductController::class, 'show']);

// API untuk menyimpan transaksi
Route::post('/transaksi', [TransactionController::class, 'store']);