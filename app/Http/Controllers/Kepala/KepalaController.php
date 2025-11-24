<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\KasHarian;
use App\Models\Produk;
use App\Models\Stok;
use App\Models\User;
use App\Models\TransaksiItem;
use App\Models\Supplier;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;

class KepalaController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Data Statistik Utama
        $data = [
            // Penjualan Hari Ini
            'penjualan_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)->sum('total_bayar') ?? 0,
            'total_transaksi_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)->count(),
            'rata_rata_transaksi' => Transaksi::whereDate('tanggal_transaksi', $today)->avg('total_bayar') ?? 0,

            // Penjualan Bulan Ini
            'penjualan_bulan_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])->sum('total_bayar') ?? 0,
            'total_transaksi_bulan_ini' => Transaksi::whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])->count(),

            // Statistik Stok
            'stok_menipis' => Produk::whereRaw('stok_tersedia <= stok_minimum')->count(),
            'total_produk' => Produk::count(),
            'produk_habis' => Produk::where('stok_tersedia', 0)->count(),
            'total_supplier' => Supplier::count(),

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

    public function getDashboardData(Request $request)
    {
        $period = $request->get('period', 'week');

        $data = [
            'penjualan' => $this->getPenjualanChartData($period),
            'produk_terlaris' => $this->getProdukTerlarisChartData($period),
            'metode_pembayaran' => $this->getMetodePembayaranData($period),
        ];

        return response()->json($data);
    }

    public function laporanKas(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Query untuk mendapatkan laporan kas harian dengan data transaksi yang sebenarnya
        $laporanKas = KasHarian::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($kasHarian) {
                // Hitung jumlah transaksi dan total pendapatan yang sebenarnya untuk tanggal tersebut
                $transaksiHariIni = Transaksi::whereDate('tanggal_transaksi', $kasHarian->tanggal)
                    ->where('status', 'Selesai')
                    ->get();

                $jumlahTransaksi = $transaksiHariIni->count();
                $totalPendapatan = $transaksiHariIni->sum('total_bayar');
                $rataRata = $jumlahTransaksi > 0 ? $totalPendapatan / $jumlahTransaksi : 0;

                return (object) [
                    'tanggal' => $kasHarian->tanggal,
                    'tanggal_formatted' => Carbon::parse($kasHarian->tanggal)->format('d M'),
                    'tanggal_full' => Carbon::parse($kasHarian->tanggal)->format('d M Y'),
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'total_pendapatan' => $totalPendapatan,
                    'rata_rata' => $rataRata,
                    'kas_harian' => $kasHarian
                ];
            });

        // Data untuk chart (menggunakan semua data, bukan paginated)
        $chartData = $laporanKas->take(30); // Batasi 30 data untuk chart agar tidak terlalu padat

        // Hitung statistik total
        $totalPendapatan = $laporanKas->sum('total_pendapatan');
        $totalTransaksi = $laporanKas->sum('jumlah_transaksi');
        $rataRata = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
        $hariAktif = $laporanKas->where('jumlah_transaksi', '>', 0)->count();

        // Pagination manual untuk tabel
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $laporanKas->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $laporanKasPaginated = new LengthAwarePaginator(
            $currentItems,
            $laporanKas->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('kepala.laporan-kas', compact(
            'laporanKasPaginated',
            'chartData',
            'startDate',
            'endDate',
            'totalPendapatan',
            'totalTransaksi',
            'rataRata',
            'hariAktif'
        ));
    }

    public function laporanPenjualan(Request $request)
    {
        // Debug: Log request parameters
        \Log::info('Laporan Penjualan Request:', $request->all());

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Pastikan end date mencakup hari ini jika tidak ada filter spesifik
        if (!$request->has('end_date')) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Query transaksi untuk pagination
        $transaksiQuery = Transaksi::with('kasir')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate)
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc');

        $transaksi = $transaksiQuery->paginate(20);

        // Query transaksi untuk chart (tanpa pagination)
        $transaksiForChart = Transaksi::with('kasir')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate)
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();

        // Hitung total item dari transaksi items
        $totalItem = TransaksiItem::whereHas('transaksi', function ($query) use ($startDate, $endDate) {
            $query->whereDate('tanggal_transaksi', '>=', $startDate)
                ->whereDate('tanggal_transaksi', '<=', $endDate)
                ->where('status', 'Selesai');
        })->sum('qty');

        // Data untuk grafik produk terlaris
        $produkTerlaris = TransaksiItem::select(
            'nama_produk',
            DB::raw('SUM(qty) as total_terjual'),
            DB::raw('SUM(subtotal) as total_penjualan')
        )
            ->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                $query->whereDate('tanggal_transaksi', '>=', $startDate)
                    ->whereDate('tanggal_transaksi', '<=', $endDate)
                    ->where('status', 'Selesai');
            })
            ->groupBy('nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        // Data untuk chart metode pembayaran
        $metodePembayaranData = $transaksiForChart->groupBy('metode_pembayaran')->map->count();

        $summary = [
            'total_penjualan' => $transaksiForChart->sum('total_bayar') ?? 0,
            'total_transaksi' => $transaksiForChart->count(),
            'rata_rata' => $transaksiForChart->avg('total_bayar') ?? 0,
            'total_item' => $totalItem,
        ];

        return view('kepala.laporan-penjualan', compact(
            'transaksi',
            'transaksiForChart',
            'summary',
            'startDate',
            'endDate',
            'produkTerlaris',
            'metodePembayaranData'
        ));
    }
    public function laporanStok(Request $request)
    {
        try {
            // Ambil parameter filter dari request
            $kategoriFilter = $request->get('kategori');
            $statusFilter = $request->get('status');
            $sortFilter = $request->get('sort');

            // Query untuk stok menipis dengan filter
            $stokMenipisQuery = Produk::with(['kategori', 'supplier'])
                ->where('stok_tersedia', '<=', DB::raw('stok_minimum'))
                ->where('stok_tersedia', '>', 0);

            // Apply kategori filter
            if ($kategoriFilter) {
                $stokMenipisQuery->where('kategori_id', $kategoriFilter);
            }

            // Apply status filter
            if ($statusFilter === 'aman') {
                $stokMenipisQuery->where('stok_tersedia', '>', DB::raw('stok_minimum'));
            } elseif ($statusFilter === 'habis') {
                $stokMenipisQuery->where('stok_tersedia', 0);
            }

            $stokMenipis = $stokMenipisQuery->orderBy('stok_tersedia')->get();

            // Query untuk produk nilai tertinggi dengan filter
            $produkNilaiTertinggiQuery = Produk::select(
                'produk_id',
                'nama_produk',
                'stok_tersedia',
                'harga_jual',
                'stok_minimum',
                DB::raw('(stok_tersedia * harga_jual) as total_nilai')
            )->where('stok_tersedia', '>', 0);

            // Apply kategori filter
            if ($kategoriFilter) {
                $produkNilaiTertinggiQuery->where('kategori_id', $kategoriFilter);
            }

            // Apply sorting
            if ($sortFilter) {
                switch ($sortFilter) {
                    case 'nama_asc':
                        $produkNilaiTertinggiQuery->orderBy('nama_produk', 'asc');
                        break;
                    case 'nama_desc':
                        $produkNilaiTertinggiQuery->orderBy('nama_produk', 'desc');
                        break;
                    case 'stok_asc':
                        $produkNilaiTertinggiQuery->orderBy('stok_tersedia', 'asc');
                        break;
                    case 'stok_desc':
                        $produkNilaiTertinggiQuery->orderBy('stok_tersedia', 'desc');
                        break;
                    case 'nilai_desc':
                        $produkNilaiTertinggiQuery->orderBy('total_nilai', 'desc');
                        break;
                    default:
                        $produkNilaiTertinggiQuery->orderBy('total_nilai', 'desc');
                }
            } else {
                $produkNilaiTertinggiQuery->orderBy('total_nilai', 'desc');
            }

            $produkNilaiTertinggi = $produkNilaiTertinggiQuery->limit(10)->get();

            // Query untuk stok per kategori
            $stokPerKategoriQuery = Produk::select(
                'kategori_produk.kategori_id',
                'kategori_produk.nama_kategori',
                DB::raw('COUNT(produk.produk_id) as jumlah_produk'),
                DB::raw('COALESCE(SUM(produk.stok_tersedia), 0) as total_stok'),
                DB::raw('COALESCE(SUM(produk.stok_tersedia * produk.harga_jual), 0) as total_nilai_stok')
            )
                ->join('kategori_produk', 'produk.kategori_id', '=', 'kategori_produk.kategori_id');

            // Apply kategori filter
            if ($kategoriFilter) {
                $stokPerKategoriQuery->where('produk.kategori_id', $kategoriFilter);
            }

            $stokPerKategori = $stokPerKategoriQuery
                ->groupBy('kategori_produk.kategori_id', 'kategori_produk.nama_kategori')
                ->orderByDesc('total_nilai_stok')
                ->get();

            // Kategori produk untuk dropdown filter
            $kategoriProduk = KategoriProduk::select(
                'kategori_id',
                'nama_kategori',
                DB::raw('(SELECT COUNT(*) FROM produk WHERE produk.kategori_id = kategori_produk.kategori_id) as total_produk')
            )
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('produk')
                        ->whereRaw('produk.kategori_id = kategori_produk.kategori_id');
                })
                ->orderByDesc('total_produk')
                ->limit(10)
                ->get();

            // Statistik lengkap dengan filter
            $totalProdukQuery = Produk::query();
            $stokAmanQuery = Produk::where('stok_tersedia', '>', DB::raw('stok_minimum'));
            $stokMenipisQueryCount = Produk::where('stok_tersedia', '<=', DB::raw('stok_minimum'))
                ->where('stok_tersedia', '>', 0);
            $stokHabisQuery = Produk::where('stok_tersedia', 0);
            $totalNilaiStokQuery = Produk::query();

            // Apply kategori filter untuk statistik
            if ($kategoriFilter) {
                $totalProdukQuery->where('kategori_id', $kategoriFilter);
                $stokAmanQuery->where('kategori_id', $kategoriFilter);
                $stokMenipisQueryCount->where('kategori_id', $kategoriFilter);
                $stokHabisQuery->where('kategori_id', $kategoriFilter);
                $totalNilaiStokQuery->where('kategori_id', $kategoriFilter);
            }

            $totalProduk = $totalProdukQuery->count();
            $totalStokSemua = $totalProdukQuery->sum('stok_tersedia');
            $stokAman = $stokAmanQuery->count();
            $stokMenipisCount = $stokMenipisQueryCount->count();
            $stokHabis = $stokHabisQuery->count();
            $totalNilaiStok = $totalNilaiStokQuery->sum(DB::raw('stok_tersedia * harga_jual'));

            $summary = [
                'total_produk' => $totalProduk,
                'total_stok_semua' => $totalStokSemua,
                'stok_aman' => $stokAman,
                'stok_menipis_count' => $stokMenipisCount,
                'stok_habis' => $stokHabis,
                'total_nilai_stok' => $totalNilaiStok,
            ];

            return view('kepala.laporan-stok', compact(
                'stokMenipis',
                'produkNilaiTertinggi',
                'stokPerKategori',
                'kategoriProduk',
                'summary'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat laporan stok: ' . $e->getMessage());
        }
    }



    public function laporanProduk()
    {
        $produk = Produk::with('kategori', 'supplier')
            ->orderBy('nama_produk')
            ->paginate(20);

        $summary = [
            'total_produk' => $produk->total(),
            'produk_tersedia' => Produk::where('status', 'Tersedia')->count(),
            'produk_habis' => Produk::where('status', 'Habis')->count(),
        ];

        return view('kepala.laporan.produk', compact('produk', 'summary'));
    }

    public function stokMenipis()
    {
        $produk = Produk::whereRaw('stok_tersedia <= stok_minimum')
            ->orderBy('stok_tersedia', 'asc')
            ->paginate(20);

        // TAMBAHKAN DATA SUMMARY YANG DIPERLUKAN
        $summary = [
            'total_produk' => Produk::count(),
            'total_stok_semua' => Produk::sum('stok_tersedia'),
            'stok_aman' => Produk::where('stok_tersedia', '>', DB::raw('stok_minimum'))->count(),
            'stok_menipis_count' => Produk::whereRaw('stok_tersedia <= stok_minimum AND stok_tersedia > 0')->count(),
            'stok_habis' => Produk::where('stok_tersedia', 0)->count(),
            'total_nilai_stok' => Produk::sum(DB::raw('stok_tersedia * harga_jual')),
        ];

        // BUAT VARIABLE KOSONG UNTUK DATA LAINNYA YANG TIDAK DIPAKAI
        $stokMenipis = $produk; // Karena ini halaman stok menipis, gunakan data yang sama
        $produkNilaiTertinggi = collect();
        $stokPerKategori = collect();
        $kategoriProduk = collect();

        return view('kepala.laporan-stok', compact(
            'stokMenipis',
            'produkNilaiTertinggi',
            'stokPerKategori',
            'kategoriProduk',
            'summary',
            'produk'
        ));
    }

    public function monitoringKasHarian()
    {
        $kas = KasHarian::with('user')
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('kepala.monitoring.kas-harian', compact('kas'));
    }

    // Method untuk detail transaksi
    public function detailTransaksi($id)
    {
        $transaksi = Transaksi::with(['items', 'kasir'])->findOrFail($id);
        return view('kepala.detail-transaksi', compact('transaksi'));
    }

    // Method untuk cetak struk
    public function cetakStruk($id)
    {
        $transaksi = Transaksi::with(['items', 'kasir'])->findOrFail($id);
        return view('kasir.struk', compact('transaksi'));
    }

    public function exportPenjualan(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Query transaksi untuk PDF (mengambil semua data tanpa pagination)
        $transaksi = Transaksi::with('kasir')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Hitung total item dari transaksi items
        $totalItem = TransaksiItem::whereHas('transaksi', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->where('status', 'Selesai');
        })->sum('qty');

        // Data untuk produk terlaris
        $produkTerlaris = TransaksiItem::select(
            'nama_produk',
            DB::raw('SUM(qty) as total_terjual'),
            DB::raw('SUM(subtotal) as total_penjualan')
        )
            ->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                    ->where('status', 'Selesai');
            })
            ->groupBy('nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        // Hitung summary
        $summary = [
            'total_penjualan' => $transaksi->sum('total_bayar') ?? 0,
            'total_transaksi' => $transaksi->count(),
            'rata_rata' => $transaksi->avg('total_bayar') ?? 0,
            'total_item' => $totalItem,
            'total_subtotal' => $transaksi->sum('subtotal'),
            'total_diskon' => $transaksi->sum('diskon'),
        ];

        // Data metode pembayaran
        $metodePembayaran = $transaksi->groupBy('metode_pembayaran')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_bayar')
            ];
        });

        $data = [
            'transaksi' => $transaksi,
            'produkTerlaris' => $produkTerlaris,
            'summary' => $summary,
            'metodePembayaran' => $metodePembayaran,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tanggalCetak' => Carbon::now()->format('d F Y H:i:s')
        ];

        $pdf = Pdf::loadView('kepala.export.penjualan-pdf', $data);
        return $pdf->download('laporan-penjualan-' . $startDate . '-sampai-' . $endDate . '.pdf');
    }

    public function exportKas(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Query untuk mendapatkan laporan kas harian dengan data transaksi yang sebenarnya
        $laporanKas = KasHarian::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($kasHarian) {
                // Hitung jumlah transaksi dan total pendapatan yang sebenarnya untuk tanggal tersebut
                $transaksiHariIni = Transaksi::whereDate('tanggal_transaksi', $kasHarian->tanggal)
                    ->where('status', 'Selesai')
                    ->get();

                $jumlahTransaksi = $transaksiHariIni->count();
                $totalPendapatan = $transaksiHariIni->sum('total_bayar');
                $rataRata = $jumlahTransaksi > 0 ? $totalPendapatan / $jumlahTransaksi : 0;

                return (object) [
                    'tanggal' => $kasHarian->tanggal,
                    'tanggal_formatted' => Carbon::parse($kasHarian->tanggal)->format('d M Y'),
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'total_pendapatan' => $totalPendapatan,
                    'rata_rata' => $rataRata,
                    'kas_harian' => $kasHarian
                ];
            });

        // Hitung statistik total
        $totalPendapatan = $laporanKas->sum('total_pendapatan');
        $totalTransaksi = $laporanKas->sum('jumlah_transaksi');
        $rataRata = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
        $hariAktif = $laporanKas->where('jumlah_transaksi', '>', 0)->count();

        $data = [
            'laporanKas' => $laporanKas,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksi' => $totalTransaksi,
            'rataRata' => $rataRata,
            'hariAktif' => $hariAktif,
            'tanggalCetak' => Carbon::now()->format('d F Y H:i:s')
        ];

        $pdf = Pdf::loadView('kepala.export.kas-pdf', $data);
        return $pdf->download('laporan-kas-' . $startDate . '-sampai-' . $endDate . '.pdf');
    }

    public function exportStok(Request $request)
    {
        try {
            // Validasi request jika diperlukan
            $request->validate([
                'type' => 'sometimes|in:pdf,excel'
            ]);

            // Gunakan query yang sama dengan laporanStok untuk konsistensi data
            $stokMenipis = Produk::with(['kategori', 'supplier'])
                ->where('stok_tersedia', '<=', DB::raw('stok_minimum'))
                ->where('stok_tersedia', '>', 0)
                ->orderBy('stok_tersedia')
                ->get();

            $produkNilaiTertinggi = Produk::select(
                'produk_id',
                'nama_produk',
                'stok_tersedia',
                'harga_jual',
                DB::raw('(stok_tersedia * harga_jual) as total_nilai')
            )
                ->where('stok_tersedia', '>', 0)
                ->orderByDesc('total_nilai')
                ->limit(10)
                ->get();

            $stokPerKategori = Produk::select(
                'kategori_produk.kategori_id',
                'kategori_produk.nama_kategori',
                DB::raw('COUNT(produk.produk_id) as jumlah_produk'),
                DB::raw('COALESCE(SUM(produk.stok_tersedia), 0) as total_stok'),
                DB::raw('COALESCE(SUM(produk.stok_tersedia * produk.harga_jual), 0) as total_nilai_stok')
            )
                ->join('kategori_produk', 'produk.kategori_id', '=', 'kategori_produk.kategori_id')
                ->groupBy('kategori_produk.kategori_id', 'kategori_produk.nama_kategori')
                ->orderByDesc('total_nilai_stok')
                ->get();

            // Statistik lengkap
            $totalProduk = Produk::count();
            $totalStokSemua = Produk::sum('stok_tersedia');
            $stokAman = Produk::where('stok_tersedia', '>', DB::raw('stok_minimum'))->count();
            $stokMenipisCount = Produk::where('stok_tersedia', '<=', DB::raw('stok_minimum'))
                ->where('stok_tersedia', '>', 0)
                ->count();
            $stokHabis = Produk::where('stok_tersedia', 0)->count();
            $totalNilaiStok = Produk::sum(DB::raw('stok_tersedia * harga_jual'));

            $summary = [
                'total_produk' => $totalProduk,
                'total_stok_semua' => $totalStokSemua,
                'stok_aman' => $stokAman,
                'stok_menipis_count' => $stokMenipisCount,
                'stok_habis' => $stokHabis,
                'total_nilai_stok' => $totalNilaiStok,
            ];


            $data = [
                'stokMenipis' => $stokMenipis,
                'produkNilaiTertinggi' => $produkNilaiTertinggi,
                'stokPerKategori' => $stokPerKategori,
                'summary' => $summary,
                'tanggalCetak' => Carbon::now()->translatedFormat('d F Y H:i:s'),
                'judulLaporan' => 'LAPORAN STOK BARANG'
            ];

            $filename = 'laporan-stok-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';

            $pdf = Pdf::loadView('kepala.export.stok-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true
                ]);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor laporan: ' . $e->getMessage());
        }
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
                    ->where('status', 'Selesai')
                    ->sum('total_bayar');
                $data[] = $total ?? 0;
            }
        } elseif ($period === 'month') {
            // Data 30 hari terakhir
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('d M');

                $total = Transaksi::whereDate('tanggal_transaksi', $date)
                    ->where('status', 'Selesai')
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
                    ->where('status', 'Selesai')
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
            ->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                    ->where('status', 'Selesai');
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
            ->where('status', 'Selesai')
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
            ->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                    ->where('status', 'Selesai');
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

    /**
     * Monitoring Kinerja Kasir
     */
    public function monitoringKasir(Request $request)
    {
        $period = $request->get('period', 'month');

        // Tentukan rentang waktu berdasarkan periode
        if ($period === 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
            $monthName = Carbon::now()->subMonth()->translatedFormat('F Y');
        } elseif ($period === 'all') {
            $startDate = null;
            $endDate = null;
            $monthName = 'Semua Waktu';
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $monthName = Carbon::now()->translatedFormat('F Y');
        }

        $query = User::where('role', 'Kasir');

        // Tambahkan kondisi untuk periode tertentu
        if ($startDate && $endDate) {
            $query->withCount(['transaksi as total_transaksi' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'Selesai')
                    ->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            }])
                ->withSum(['transaksi as total_penjualan' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 'Selesai')
                        ->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
                }], 'total_bayar');
        } else {
            // Untuk semua waktu
            $query->withCount(['transaksi as total_transaksi' => function ($query) {
                $query->where('status', 'Selesai');
            }])
                ->withSum(['transaksi as total_penjualan' => function ($query) {
                    $query->where('status', 'Selesai');
                }], 'total_bayar');
        }

        $kasir = $query->orderBy('total_penjualan', 'desc')
            ->paginate(10);

        // Hitung total statistik
        $totalTransaksi = $kasir->sum('total_transaksi');
        $totalPenjualan = $kasir->sum('total_penjualan');
        $rataRata = $totalTransaksi > 0 ? $totalPenjualan / $totalTransaksi : 0;

        // DATA BARU: Trend Penjualan 7 Hari Terakhir
        $trendPenjualan = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $totalHari = Transaksi::whereDate('tanggal_transaksi', $date)
                ->where('status', 'Selesai')
                ->sum('total_bayar') ?? 0;

            $trendPenjualan[] = [
                'label' => $date->format('d M'),
                'total' => $totalHari
            ];
        }

        // DATA BARU: Kategori Produk Terlaris
        $kategoriTerlaris = TransaksiItem::select(
            'produk.kategori_id',
            'kategori_produk.nama_kategori',
            DB::raw('SUM(transaksi_items.qty) as total_terjual'),
            DB::raw('SUM(transaksi_items.subtotal) as total_penjualan')
        )
            ->join('produk', 'transaksi_items.produk_id', '=', 'produk.produk_id')
            ->join('kategori_produk', 'produk.kategori_id', '=', 'kategori_produk.kategori_id')
            ->join('transaksi', 'transaksi_items.transaksi_id', '=', 'transaksi.transaksi_id')
            ->where('transaksi.status', 'Selesai')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate]);
            })
            ->groupBy('produk.kategori_id', 'kategori_produk.nama_kategori')
            ->orderBy('total_penjualan', 'desc')
            ->limit(5)
            ->get();

        // DATA BARU: Jam Puncak Transaksi
        $jamPuncak = Transaksi::select(
            DB::raw('HOUR(tanggal_transaksi) as jam'),
            DB::raw('COUNT(*) as total_transaksi'),
            DB::raw('SUM(total_bayar) as total_penjualan')
        )
            ->where('status', 'Selesai')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->groupBy('jam')
            ->orderBy('total_transaksi', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                return [
                    'jam' => $item->jam . ':00',
                    'total_transaksi' => $item->total_transaksi,
                    'total_penjualan' => $item->total_penjualan
                ];
            });

        return view('kepala.monitoring.kasir', compact(
            'kasir',
            'totalTransaksi',
            'totalPenjualan',
            'rataRata',
            'period',
            'monthName',
            'trendPenjualan',
            'kategoriTerlaris',
            'jamPuncak'
        ));
    }


    /**
     * Laporan Produk Terlaris
     */
    public function laporanProdukTerlaris(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $produkTerlaris = TransaksiItem::select(
            'produk_id',
            'nama_produk',
            DB::raw('SUM(qty) as total_terjual'),
            DB::raw('SUM(subtotal) as total_penjualan')
        )
            ->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                    ->where('status', 'Selesai');
            })
            ->groupBy('produk_id', 'nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->paginate(20);

        $summary = [
            'total_produk_terjual' => $produkTerlaris->sum('total_terjual'),
            'total_penjualan' => $produkTerlaris->sum('total_penjualan'),
            'produk_terbanyak' => $produkTerlaris->first()->nama_produk ?? '-',
        ];

        return view('kepala.laporan.produk-terlaris', compact('produkTerlaris', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Debug method untuk mengecek transaksi terbaru
     */
    public function debugTransactions()
    {
        $latestTransactions = Transaksi::with('kasir')
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'count' => $latestTransactions->count(),
            'transactions' => $latestTransactions->map(function ($trx) {
                return [
                    'no_transaksi' => $trx->no_transaksi,
                    'tanggal_transaksi' => $trx->tanggal_transaksi,
                    'total_bayar' => $trx->total_bayar,
                    'status' => $trx->status,
                    'kasir' => $trx->kasir->nama ?? 'N/A'
                ];
            })
        ]);
    }
}
