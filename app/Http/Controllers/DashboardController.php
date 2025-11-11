<?php
namespace App\Http\Controllers; // <-- INI YANG DIPERBAIKI

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller 
{
    public function index() 
    {
        // --- 1. KPI Cards (MENGGUNAKAN 'total_bayar') ---
        $penjualanHariIni = Transaction::whereDate('created_at', today())->sum('total_bayar');
        $kemarin = Transaction::whereDate('created_at', today()->subDay())->sum('total_bayar');
        $persenHariIni = ($kemarin > 0) ? (($penjualanHariIni - $kemarin) / $kemarin) * 100 : ($penjualanHariIni > 0 ? 100 : 0);

        $penjualanMingguIni = Transaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_bayar');
        $mingguLalu = Transaction::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('total_bayar');
        $persenMingguIni = ($mingguLalu > 0) ? (($penjualanMingguIni - $mingguLalu) / $mingguLalu) * 100 : ($penjualanMingguIni > 0 ? 100 : 0);
        
        $penjualanBulanIni = Transaction::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_bayar');
        $bulanLalu = Transaction::whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->sum('total_bayar');
        $persenBulanIni = ($bulanLalu > 0) ? (($penjualanBulanIni - $bulanLalu) / $bulanLalu) * 100 : ($penjualanBulanIni > 0 ? 100 : 0);

        $transaksiHariIni = Transaction::whereDate('created_at', today())->count();
        $avgTransaksi = ($transaksiHariIni > 0) ? $penjualanHariIni / $transaksiHariIni : 0;
        
        $kpiData = [
            'penjualanHariIni' => $penjualanHariIni, 'persenHariIni' => $persenHariIni,
            'penjualanMingguIni' => $penjualanMingguIni, 'persenMingguIni' => $persenMingguIni,
            'penjualanBulanIni' => $penjualanBulanIni, 'persenBulanIni' => $persenBulanIni,
            'transaksiHariIni' => $transaksiHariIni, 'avgTransaksi' => $avgTransaksi,
        ];

        // --- 2. Produk Terlaris (Bulan Ini) (MENGGUNAKAN 'produk_id' & 'nama_produk') ---
        $topProducts = TransactionDetail::select('produk_id', DB::raw('SUM(jumlah) as total_terjual'), DB::raw('SUM(subtotal) as total_rupiah'))
            ->whereHas('transaction', function($q) {
                $q->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);
            })
            ->with('product:produk_id,nama_produk')
            ->groupBy('produk_id')
            ->orderBy('total_terjual', 'desc')
            ->limit(3)
            ->get();
            
        // --- 3. Notifikasi Stok (MENGGUNAKAN 'stok_tersedia' & 'stok_minimum') ---
        $stokMenipis = Product::whereColumn('stok_tersedia', '<', 'stok_minimum')
            ->orderBy('stok_tersedia', 'asc')
            ->limit(5)
            ->get();

        // --- 4. Analisis Kategori (MENGGUNAKAN 'kategori_id') ---
        $kategoriSales = TransactionDetail::join('produk', 'detail_transaksi.produk_id', '=', 'produk.produk_id')
            ->join('kategori_produk', 'produk.kategori_id', '=', 'kategori_produk.kategori_id')
            ->select('kategori_produk.nama_kategori', DB::raw('SUM(detail_transaksi.subtotal) as total_penjualan'))
            ->groupBy('kategori_produk.nama_kategori')
            ->orderBy('total_penjualan', 'desc')
            ->get();
            
        // Kirim semua data ke view
        return view('dashboard', [
            'kpi' => $kpiData,
            'topProducts' => $topProducts,
            'stokMenipis' => $stokMenipis,
            'kategoriSales' => $kategoriSales
        ]);
    }
}