<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Produk::count();
        $totalSupplier = Supplier::count();
        $totalTransaksi = Transaksi::count();

        // Ambil produk yang stoknya sudah menipis
        $stokMenipis = Produk::whereColumn('stok_tersedia', '<=', 'stok_minimum')
            ->where('status', '!=', 'Nonaktif')
            ->get();

        return view('admin.dashboard', compact('totalProduk', 'totalSupplier', 'totalTransaksi', 'stokMenipis'));
    }
}
