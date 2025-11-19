<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Kasir\DashboardController;
use App\Http\Controllers\Kasir\RiwayatController;
use App\Http\Controllers\Kasir\KasHarianController;
use App\Http\Controllers\Kasir\TransaksiItemController;
use App\Http\Controllers\Kasir\PengeluaranController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ArsipController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ====================
// Redirect otomatis ke login
// ====================
Route::get('/', function () {
    return redirect()->route('login');
});

// ====================
// Halaman Login & Logout
// ====================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ====================
// Role: Admin Toko
// ====================
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // === Manajemen Produk ===
    Route::prefix('produk')->name('produk.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ProdukController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ProdukController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\ProdukController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\ProdukController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\ProdukController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ProdukController::class, 'destroy'])->name('destroy');
    });

    // === Management Kategori ===
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\KategoriController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\KategoriController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\KategoriController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\KategoriController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\KategoriController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\KategoriController::class, 'destroy'])->name('destroy');
    });

    /// === Stok Barang ===
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StokController::class, 'index'])->name('index');
        Route::post('/store', [App\Http\Controllers\Admin\StokController::class, 'store'])->name('store');
        Route::post('/masuk', [App\Http\Controllers\Admin\StokController::class, 'masuk'])->name('masuk');
        Route::post('/keluar', [App\Http\Controllers\Admin\StokController::class, 'keluar'])->name('keluar');
    });

    // === Supplier ===
    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\SupplierController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\SupplierController::class, 'store'])->name('store');

        // ✅ FIX: Letakkan routes dengan parameter spesifik DULU
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\SupplierController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\SupplierController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\SupplierController::class, 'destroy'])->name('destroy');

        // ✅ Route show diakhir (catch-all)
        Route::get('/{id}', [App\Http\Controllers\Admin\SupplierController::class, 'show'])->name('show');
    });

    // routes/web.php - PERBAIKAN ROUTE ARSIP
    Route::prefix('arsip')->name('arsip.')->group(function () {
        Route::get('/', [ArsipController::class, 'index'])->name('index');
        Route::get('/upload', [ArsipController::class, 'create'])->name('create');
        Route::post('/upload', [ArsipController::class, 'upload'])->name('upload');
        Route::get('/file/{id}/view', [ArsipController::class, 'showFile'])->name('file.view'); // BARU
        Route::get('/file/{id}/download', [ArsipController::class, 'downloadFile'])->name('file.download'); // BARU
    });

    // Laporan Manajerial Routes - PERBAIKAN
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/create', [LaporanController::class, 'create'])->name('create');
        Route::post('/', [LaporanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LaporanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LaporanController::class, 'update'])->name('update');
        Route::delete('/{id}', [LaporanController::class, 'destroy'])->name('destroy');
        Route::post('/cetak-quick', [LaporanController::class, 'cetakQuickLaporan'])->name('cetak-quick');
    });
});

// ====================
// Role: Kasir
// ====================
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:Kasir'])->group(function () {
    // Main Kasir - Transaksi
    Route::get('/', [KasirController::class, 'index'])->name('index');

    // ==================== DASHBOARD ROUTES ====================
    Route::get('/dashboard', [DashboardController::class, 'dashboardKas'])->name('dashboard');

    // Dashboard sub-routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/statistik', [DashboardController::class, 'getStatistik'])->name('statistik');
        Route::get('/laporan-harian', [DashboardController::class, 'laporanHarian'])->name('laporan-harian');
        Route::get('/cek-status-kas', [DashboardController::class, 'cekStatusKas'])->name('cek-status-kas');
        Route::get('/data', [DashboardController::class, 'getDashboardData'])->name('data'); // TAMBAHAN
        // HAPUS: Route::post('/buka-kas', [DashboardController::class, 'bukaKasFromDashboard'])->name('buka-kas');
    });

    // Produk Management (Read-Only)
    Route::prefix('produk')->group(function () {
        Route::get('/daftar', [KasirController::class, 'daftarProduk'])->name('daftar-produk');
        Route::get('/low-stock-alert', [KasirController::class, 'getLowStockAlert'])->name('low-stock-alert');
        Route::get('/detail/{id}', [KasirController::class, 'getProductDetail'])->name('product-detail');
        Route::get('/suggested', [KasirController::class, 'getSuggestedProducts'])->name('suggested-products');
    });

    // Cart Management
    Route::prefix('cart')->group(function () {
        Route::post('/add', [KasirController::class, 'addToCart'])->name('add-to-cart');
        Route::post('/quick-add', [KasirController::class, 'quickAddToCart'])->name('quick-add-to-cart');
        Route::post('/bulk-add', [KasirController::class, 'bulkAddToCart'])->name('bulk-add-to-cart');
        Route::post('/update-item', [KasirController::class, 'updateCartItem'])->name('update-cart-item');
        Route::post('/remove-item', [KasirController::class, 'removeFromCart'])->name('remove-from-cart');
        Route::get('/summary', [KasirController::class, 'getCartSummary'])->name('cart-summary');
        Route::get('/validate-stock', [KasirController::class, 'validateStock'])->name('validate-stock');
    });

    // Search & Discovery
    Route::prefix('search')->group(function () {
        Route::post('/quick', [KasirController::class, 'quickSearch'])->name('quick-search');
        Route::get('/clear', [KasirController::class, 'clearSearchResults'])->name('clear-search');
    });

    // Diskon
    Route::prefix('diskon')->group(function () {
        Route::post('/apply', [KasirController::class, 'applyDiskon'])->name('apply-diskon');
        Route::post('/apply-persen', [KasirController::class, 'applyDiskonPersen'])->name('apply-diskon-persen');
    });

    // Transaksi
    Route::prefix('transaksi')->group(function () {
        Route::post('/proses', [KasirController::class, 'prosesTransaksi'])->name('proses-transaksi');
        Route::get('/reset', [KasirController::class, 'resetTransaksi'])->name('reset-transaksi');
        Route::get('/cetak-struk/{id}', [KasirController::class, 'cetakStruk'])->name('cetak-struk');
    });

    // Riwayat Transaksi
    Route::prefix('riwayat')->group(function () {
        Route::get('/', [RiwayatController::class, 'index'])->name('riwayat');
        Route::get('/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
        Route::get('/{id}/detail', [RiwayatController::class, 'getTransaksiDetail'])->name('riwayat.detail');
        Route::post('/batalkan/{id}', [RiwayatController::class, 'batalkanTransaksi'])->name('riwayat.batalkan');
        Route::post('/export', [RiwayatController::class, 'export'])->name('riwayat.export');

        // Hapus Transaksi
        Route::delete('/hapus/{id}', [RiwayatController::class, 'hapusTransaksi'])->name('riwayat.hapus');
        Route::post('/hapus-multiple', [RiwayatController::class, 'hapusMultipleTransaksi'])->name('riwayat.hapus-multiple');
    });

    // ==================== KAS HARIAN ROUTES ====================
    Route::prefix('kas-harian')->name('kas-harian.')->group(function () {
        // Main Routes - BUKA KAS VIEW
        Route::get('/', [KasHarianController::class, 'index'])->name('index');

        // ✅ PERBAIKAN: Route buka kas yang benar
        Route::post('/buka', [KasHarianController::class, 'bukaKas'])->name('buka'); // nama route: kasir.kas-harian.buka

        Route::get('/laporan/{id?}', [KasHarianController::class, 'laporan'])->name('laporan');

        // CETAK LAPORAN
        Route::get('/cetak-laporan/{id?}', [KasHarianController::class, 'cetakLaporanHarian'])
            ->name('cetak-laporan');

        // Aksi Kas
        Route::post('/tutup', [KasHarianController::class, 'tutupKas'])->name('tutup');

        // Transaksi dalam Kas
        Route::post('/pengeluaran', [KasHarianController::class, 'tambahPengeluaran'])->name('pengeluaran.tambah');

        // API
        Route::get('/status', [KasHarianController::class, 'cekStatus'])->name('status');
        Route::get('/refresh', [KasHarianController::class, 'refresh'])->name('refresh');

        // Detail & Reset
        Route::get('/{id}', [KasHarianController::class, 'show'])->name('show');
        Route::post('/reset-hari-ini', [KasHarianController::class, 'resetKasHariIni'])->name('reset-hari-ini');
    });

    // ==================== ANALYTICS ROUTES ====================
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/transaksi-items/{transaksiId}', [TransaksiItemController::class, 'getByTransaksi'])->name('transaksi-items.by-transaksi');
        Route::get('/top-products', [TransaksiItemController::class, 'getTopProducts'])->name('top-products');
        Route::get('/sales-by-category', [TransaksiItemController::class, 'getSalesByCategory'])->name('sales-by-category');
        Route::get('/daily-sales', [TransaksiItemController::class, 'getDailySales'])->name('daily-sales');
        Route::put('/transaksi-items/{itemId}/update-qty', [TransaksiItemController::class, 'updateQty'])->name('transaksi-items.update-qty');
    });

    // ==================== TESTING ROUTES ====================
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('/search', [KasirController::class, 'testSearchFunction'])->name('search');
        Route::get('/database', [KasirController::class, 'testDatabase'])->name('database');
    });

    // ==================== PRODUCT IMAGE ROUTES ====================
    Route::prefix('produk')->group(function () {
        Route::get('/daftar', [KasirController::class, 'daftarProduk'])->name('daftar-produk');
        Route::get('/low-stock-alert', [KasirController::class, 'getLowStockAlert'])->name('low-stock-alert');
        Route::get('/detail/{id}', [KasirController::class, 'getProductDetail'])->name('product-detail');
        Route::get('/suggested', [KasirController::class, 'getSuggestedProducts'])->name('suggested-products');

        // ✅ TAMBAHAN ROUTES UNTUK GAMBAR
        Route::get('/image-check/{id}', [KasirController::class, 'checkProductImage'])->name('product-image-check');
        Route::get('/with-images', [KasirController::class, 'getProductsWithImages'])->name('products-with-images');
        Route::post('/quick-add-from-list', [KasirController::class, 'quickAddToCartFromList'])->name('quick-add-from-list');
    });

    // ==================== PENGELUARAN ROUTES ====================
    // Tambahkan route ini di web.php dalam group kasir

    Route::prefix('pengeluaran')->name('pengeluaran.')->group(function () {
        Route::get('/', [PengeluaranController::class, 'index'])->name('index');
        Route::get('/create', [PengeluaranController::class, 'create'])->name('create');
        Route::post('/', [PengeluaranController::class, 'store'])->name('store');
        Route::get('/{id}', [PengeluaranController::class, 'show'])->name('show');
        Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('destroy');
        Route::get('/laporan', [PengeluaranController::class, 'laporan'])->name('laporan');
    });

    // ==================== ROUTES KOMPATIBILITAS ====================
    Route::post('/quick-search', [KasirController::class, 'quickSearch'])->name('quick-search');
    Route::post('/add-to-cart', [KasirController::class, 'addToCart'])->name('add-to-cart');
    Route::post('/quick-add-to-cart', [KasirController::class, 'quickAddToCart'])->name('quick-add-to-cart');
    Route::post('/update-cart-item', [KasirController::class, 'updateCartItem'])->name('update-cart-item');
    Route::post('/remove-from-cart', [KasirController::class, 'removeFromCart'])->name('remove-from-cart');
    Route::post('/apply-diskon', [KasirController::class, 'applyDiskon'])->name('apply-diskon');
    Route::post('/apply-diskon-persen', [KasirController::class, 'applyDiskonPersen'])->name('apply-diskon-persen');
    Route::get('/reset-transaksi', [KasirController::class, 'resetTransaksi'])->name('reset-transaksi');
    Route::post('/proses-transaksi', [KasirController::class, 'prosesTransaksi'])->name('proses-transaksi');
    Route::get('/cetak-struk/{id}', [KasirController::class, 'cetakStruk'])->name('cetak-struk');
    Route::get('/clear-search', [KasirController::class, 'clearSearchResults'])->name('clear-search');
    Route::get('/cart-summary', [KasirController::class, 'getCartSummary'])->name('cart-summary');
    Route::get('/validate-stock', [KasirController::class, 'validateStock'])->name('validate-stock');
    Route::get('/suggested-products', [KasirController::class, 'getSuggestedProducts'])->name('suggested-products');
    Route::post('/bulk-add-to-cart', [KasirController::class, 'bulkAddToCart'])->name('bulk-add-to-cart');

    // Kompatibilitas kas - PERBAIKI INI
    // HAPUS: Route::post('/buka-kas', [KasHarianController::class, 'bukaKas'])->name('buka-kas');
    Route::post('/tutup-kas/{id}', [KasHarianController::class, 'tutupKas'])->name('tutup-kas');
    Route::post('/kas-harian/reset-hari-ini', [KasHarianController::class, 'resetKasHariIni'])->name('kas-harian.reset-hari-ini');
});

// ====================
// Role: Kepala Minimarket
// ====================
Route::prefix('kepala')->name('kepala.')->middleware(['auth', 'role:Kepala'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');

    // Laporan
    Route::get('/laporan/kas', [DashboardController::class, 'laporanKas'])->name('laporan.kas');
    Route::get('/laporan/penjualan', [DashboardController::class, 'laporanPenjualan'])->name('laporan.penjualan');

    // Management routes bisa ditambahkan later
});
