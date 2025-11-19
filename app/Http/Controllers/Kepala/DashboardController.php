<?php
// app/Http/Controllers\Kepala\DashboardController.php
namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\KasHarian;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Pengeluaran;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard utama kepala minimarket
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Data kas harian
        $kasHarian = KasHarian::where('tanggal', $today)->first();
        $kasMingguIni = KasHarian::whereBetween('tanggal', [$startOfWeek, $today])->get();
        $kasBulanIni = KasHarian::whereBetween('tanggal', [$startOfMonth, $today])->get();

        // Statistik utama
        $stats = $this->getDashboardStats($today, $startOfWeek, $startOfMonth);

        // Performance metrics
        $performance = $this->getPerformanceMetrics($stats);

        // Data untuk charts
        $chartData = $this->getChartData();

        // Kasir aktif hari ini
        $kasirAktif = $this->getKasirAktif();

        // Produk terlaris
        $produkTerlaris = $this->getProdukTerlaris();

        // Transaksi terbaru
        $transaksiTerbaru = Transaksi::with(['items', 'kasir'])
            ->whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(10)
            ->get();

        // Pengeluaran besar hari ini
        $pengeluaranBesar = Pengeluaran::whereDate('tanggal', $today)
            ->where('jumlah', '>=', 100000) // Pengeluaran > 100k
            ->orderBy('jumlah', 'desc')
            ->limit(10)
            ->get();

        return view('kepala.dashboard', compact(
            'kasHarian',
            'stats',
            'performance',
            'chartData',
            'kasirAktif',
            'produkTerlaris',
            'transaksiTerbaru',
            'pengeluaranBesar',
            'kasMingguIni',
            'kasBulanIni'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($today, $startOfWeek, $startOfMonth)
    {
        return [
            // Hari Ini
            'transaksi_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)
                ->where('status', 'Selesai')->count(),
            'penjualan_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)
                ->where('status', 'Selesai')->sum('total_bayar') ?? 0,
            'produk_terjual_hari_ini' => TransaksiItem::whereHas('transaksi', function($q) use ($today) {
                $q->whereDate('tanggal_transaksi', $today)->where('status', 'Selesai');
            })->sum('qty') ?? 0,

            // Minggu Ini
            'transaksi_minggu_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfWeek, $today])
                ->where('status', 'Selesai')->count(),
            'penjualan_minggu_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfWeek, $today])
                ->where('status', 'Selesai')->sum('total_bayar') ?? 0,

            // Bulan Ini
            'transaksi_bulan_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfMonth, $today])
                ->where('status', 'Selesai')->count(),
            'penjualan_bulan_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfMonth, $today])
                ->where('status', 'Selesai')->sum('total_bayar') ?? 0,

            // Stok
            'total_produk' => Produk::where('status', 'Tersedia')->count(),
            'stok_menipis' => Produk::where('status', 'Tersedia')
                ->whereRaw('stok_tersedia <= stok_minimum')
                ->where('stok_tersedia', '>', 0)->count(),
            'stok_habis' => Produk::where('status', 'Tersedia')
                ->where('stok_tersedia', 0)->count(),

            // Kas
            'kas_hari_ini' => KasHarian::where('tanggal', $today)->sum('saldo_akhir') ?? 0,
            'pengeluaran_hari_ini' => Pengeluaran::whereDate('tanggal', $today)->sum('jumlah') ?? 0,
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($stats)
    {
        return [
            'rata_transaksi_hari' => $stats['transaksi_hari_ini'] > 0 ? 
                $stats['penjualan_hari_ini'] / $stats['transaksi_hari_ini'] : 0,
            'growth_minggu' => $this->calculateGrowth($stats['penjualan_minggu_ini'], 'week'),
            'growth_bulan' => $this->calculateGrowth($stats['penjualan_bulan_ini'], 'month'),
            'konversi_stok' => $stats['total_produk'] > 0 ? 
                ($stats['stok_menipis'] / $stats['total_produk']) * 100 : 0,
            'profit_margin' => $this->calculateProfitMargin($stats),
        ];
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($current, $period)
    {
        $previous = 0;
        
        if ($period === 'week') {
            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
            $previous = Transaksi::whereBetween('tanggal_transaksi', [$lastWeekStart, $lastWeekEnd])
                ->where('status', 'Selesai')->sum('total_bayar') ?? 0;
        } else {
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
            $previous = Transaksi::whereBetween('tanggal_transaksi', [$lastMonthStart, $lastMonthEnd])
                ->where('status', 'Selesai')->sum('total_bayar') ?? 0;
        }

        if ($previous == 0) return 100; // Jika tidak ada data sebelumnya
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Calculate profit margin (simplified)
     */
    private function calculateProfitMargin($stats)
    {
        $revenue = $stats['penjualan_hari_ini'];
        $cogs = $revenue * 0.7; // Asumsi COGS 70%
        $profit = $revenue - $cogs;
        
        return $revenue > 0 ? ($profit / $revenue) * 100 : 0;
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        $data = [];
        
        // Sales last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sales = Transaksi::whereDate('tanggal_transaksi', $date)
                ->where('status', 'Selesai')
                ->sum('total_bayar') ?? 0;
                
            $data['sales_7_days']['labels'][] = $date->format('D');
            $data['sales_7_days']['data'][] = $sales;
        }

        // Top categories (example)
        $categories = TransaksiItem::select('nama_produk', DB::raw('SUM(qty) as total'))
            ->whereHas('transaksi', function($q) {
                $q->where('status', 'Selesai')
                  ->whereDate('tanggal_transaksi', '>=', Carbon::now()->subWeek());
            })
            ->groupBy('nama_produk')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        foreach ($categories as $item) {
            $data['top_products']['labels'][] = $item->nama_produk;
            $data['top_products']['data'][] = $item->total;
        }

        return $data;
    }

    /**
     * Get active kasir today
     */
    private function getKasirAktif()
    {
        return User::where('role', 'Kasir')
            ->whereHas('transaksi', function($q) {
                $q->whereDate('tanggal_transaksi', today())
                  ->where('status', 'Selesai');
            })
            ->withCount(['transaksi' => function($q) {
                $q->whereDate('tanggal_transaksi', today())
                  ->where('status', 'Selesai');
            }])
            ->withSum(['transaksi' => function($q) {
                $q->whereDate('tanggal_transaksi', today())
                  ->where('status', 'Selesai');
            }], 'total_bayar')
            ->orderBy('transaksi_sum_total_bayar', 'desc')
            ->get();
    }

    /**
     * Get top selling products
     */
    private function getProdukTerlaris()
    {
        return TransaksiItem::select(
                'produk_id',
                'nama_produk',
                DB::raw('SUM(qty) as total_terjual'),
                DB::raw('SUM(subtotal) as total_pendapatan')
            )
            ->whereHas('transaksi', function($q) {
                $q->where('status', 'Selesai')
                  ->whereDate('tanggal_transaksi', '>=', Carbon::now()->subWeek());
            })
            ->groupBy('produk_id', 'nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get dashboard data for AJAX
     */
    public function getDashboardData(Request $request)
    {
        $today = Carbon::today();
        $stats = $this->getDashboardStats($today, 
            Carbon::now()->startOfWeek(), 
            Carbon::now()->startOfMonth()
        );

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'performance' => $this->getPerformanceMetrics($stats),
            'last_updated' => now()->format('H:i:s')
        ]);
    }

    /**
     * Get kas harian report
     */
    public function laporanKas()
    {
        $startDate = request('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', Carbon::now()->format('Y-m-d'));

        $kasHarian = KasHarian::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        $summary = [
            'total_saldo_awal' => $kasHarian->sum('saldo_awal'),
            'total_penerimaan_tunai' => $kasHarian->sum('penerimaan_tunai'),
            'total_penerimaan_non_tunai' => $kasHarian->sum('penerimaan_non_tunai'),
            'total_pengeluaran' => $kasHarian->sum('pengeluaran'),
            'total_saldo_akhir' => $kasHarian->sum('saldo_akhir'),
        ];

        return view('kepala.laporan-kas', compact('kasHarian', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Get sales report
     */
    public function laporanPenjualan()
    {
        $startDate = request('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', Carbon::now()->format('Y-m-d'));

        $transaksi = Transaksi::with(['items', 'kasir'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $summary = [
            'total_transaksi' => $transaksi->count(),
            'total_penjualan' => $transaksi->sum('total_bayar'),
            'rata_rata_transaksi' => $transaksi->avg('total_bayar'),
            'total_item_terjual' => $transaksi->sum('total_item'),
        ];

        // Group by metode pembayaran
        $metodePembayaran = $transaksi->groupBy('metode_pembayaran')->map(function($group, $metode) {
            return [
                'metode' => $metode,
                'total' => $group->count(),
                'jumlah' => $group->sum('total_bayar')
            ];
        })->values();

        return view('kepala.laporan-penjualan', compact('transaksi', 'summary', 'metodePembayaran', 'startDate', 'endDate'));
    }
}