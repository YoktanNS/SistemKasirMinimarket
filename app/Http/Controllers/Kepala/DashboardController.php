<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\KasHarian;
use App\Models\Produk;
use App\Models\Stok;
use App\Models\User;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard utama kepala minimarket
     */
    public function dashboard()
    {
        // Pastikan hanya kepala yang bisa akses
        if (!auth()->user()->isKepala()) {
            abort(403, 'Unauthorized access.');
        }

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Data Statistik Utama
        $data = [
            // Penjualan Hari Ini
            'penjualan_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)->sum('total_bayar'),
            'total_transaksi_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)->count(),
            'rata_rata_transaksi' => Transaksi::whereDate('tanggal_transaksi', $today)->avg('total_bayar') ?? 0,
            
            // Penjualan Bulan Ini
            'penjualan_bulan_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])->sum('total_bayar'),
            'total_transaksi_bulan_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])->count(),
            
            // Statistik Stok
            'stok_menipis' => Produk::whereRaw('stok_tersedia <= stok_minimum')->count(),
            'total_produk' => Produk::count(),
            'produk_habis' => Produk::where('stok_tersedia', 0)->count(),
            'total_supplier' => \App\Models\Supplier::count(),
            
            // Kas Harian
            'kas_hari_ini' => KasHarian::whereDate('tanggal', $today)->first(),
            'status_kas' => KasHarian::whereDate('tanggal', $today)->value('status') ?? 'Belum Dibuka',
            
            // Produk Terlaris Hari Ini
            'produk_terlaris_hari_ini' => $this->getProdukTerlaris($today, $today, 5),
            
            // Transaksi Terbaru
            'transaksi_terbaru' => Transaksi::with('kasir')
                ->whereDate('tanggal_transaksi', $today)
                ->orderBy('tanggal_transaksi', 'desc')
                ->limit(5)
                ->get(),
        ];

        return view('kepala.dashboard', $data);
    }

    /**
     * API Data untuk chart dashboard
     */
    public function getDashboardData(Request $request)
    {
        if (!auth()->user()->isKepala()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $period = $request->get('period', 'week'); // week, month, year
        
        $data = [
            'penjualan' => $this->getPenjualanChartData($period),
            'produk_terlaris' => $this->getProdukTerlarisChartData($period),
            'metode_pembayaran' => $this->getMetodePembayaranData($period),
        ];

        return response()->json($data);
    }

    /**
     * Laporan Kas
     */
    public function laporanKas(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $kas = KasHarian::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        $summary = [
            'total_penerimaan' => $kas->sum('total_penerimaan'),
            'total_pengeluaran' => $kas->sum('pengeluaran'),
            'saldo_akhir_rata' => $kas->avg('saldo_akhir'),
        ];

        return view('kepala.laporan.kas', compact('kas', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Laporan Penjualan
     */
    public function laporanPenjualan(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $transaksi = Transaksi::with('kasir')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(20);

        $summary = [
            'total_penjualan' => $transaksi->sum('total_bayar'),
            'total_transaksi' => $transaksi->total(),
            'rata_rata' => $transaksi->avg('total_bayar') ?? 0,
            'total_item' => TransaksiItem::whereHas('transaksi', function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })->sum('qty'),
        ];

        return view('kepala.laporan.penjualan', compact('transaksi', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Data Chart Penjualan
     */
    private function getPenjualanChartData($period)
    {
        $data = [];
        $labels = [];

        if ($period === 'week') {
            // Data 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('D');
                
                $total = Transaksi::whereDate('tanggal_transaksi', $date)
                    ->sum('total_bayar');
                $data[] = $total ?? 0;
            }
        } elseif ($period === 'month') {
            // Data 30 hari terakhir
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('d M');
                
                $total = Transaksi::whereDate('tanggal_transaksi', $date)
                    ->sum('total_bayar');
                $data[] = $total ?? 0;
            }
        } else { // year
            // Data 12 bulan terakhir
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M Y');
                
                $total = Transaksi::whereYear('tanggal_transaksi', $date->year)
                    ->whereMonth('tanggal_transaksi', $date->month)
                    ->sum('total_bayar');
                $data[] = $total ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Data Produk Terlaris untuk Chart
     */
    private function getProdukTerlarisChartData($period)
    {
        $startDate = $this->getStartDateByPeriod($period);
        $endDate = Carbon::now();

        $produkTerlaris = TransaksiItem::select(
                'nama_produk',
                DB::raw('SUM(qty) as total_terjual'),
                DB::raw('SUM(subtotal) as total_penjualan')
            )
            ->whereHas('transaksi', function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->groupBy('nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $produkTerlaris->pluck('nama_produk'),
            'data' => $produkTerlaris->pluck('total_terjual'),
            'penjualan' => $produkTerlaris->pluck('total_penjualan')
        ];
    }

    /**
     * Data Metode Pembayaran
     */
    private function getMetodePembayaranData($period)
    {
        $startDate = $this->getStartDateByPeriod($period);
        $endDate = Carbon::now();

        $metodePembayaran = Transaksi::select(
                'metode_pembayaran',
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(total_bayar) as total_penjualan')
            )
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('metode_pembayaran')
            ->get();

        return [
            'labels' => $metodePembayaran->pluck('metode_pembayaran'),
            'data' => $metodePembayaran->pluck('total_transaksi'),
            'penjualan' => $metodePembayaran->pluck('total_penjualan')
        ];
    }

    /**
     * Helper: Get produk terlaris
     */
    private function getProdukTerlaris($startDate, $endDate, $limit = 5)
    {
        return TransaksiItem::select(
                'nama_produk',
                DB::raw('SUM(qty) as total_terjual'),
                DB::raw('SUM(subtotal) as total_penjualan')
            )
            ->whereHas('transaksi', function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->groupBy('nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Helper: Get start date berdasarkan period
     */
    private function getStartDateByPeriod($period)
    {
        switch ($period) {
            case 'week':
                return Carbon::now()->subWeek();
            case 'month':
                return Carbon::now()->subMonth();
            case 'year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subWeek();
        }
    }
}