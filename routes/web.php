<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\DashboardController; // Kita akan buat ini nanti

// Halaman TPS (Kasir)
Route::get('/', [PosController::class, 'index'])->name('tps');

// Halaman MIS (Dashboard)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');