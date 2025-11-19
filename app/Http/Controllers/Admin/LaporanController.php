<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\TransaksiItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan utama
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'hari-ini');
        $tanggalAwal = $request->get('tanggal_awal', Carbon::today()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::today()->format('Y-m-d'));

        // Filter transaksi berdasarkan periode
        $query = $this->getFilteredQuery($periode, $tanggalAwal, $tanggalAkhir);

        // 1. STATISTIK UTAMA
        $statistik = $this->getStatistikUtama($query, $periode, $tanggalAwal, $tanggalAkhir);

        // 2. PRODUK TERLARIS
        $produkTerlaris = $this->getProdukTerlaris($query);

        // 3. PERFORMANSI KASIR
        $performansiKasir = $this->getPerformansiKasir($query);

        return view('admin.laporan.index', compact(
            'statistik',
            'produkTerlaris',
            'performansiKasir',
            'periode',
            'tanggalAwal',
            'tanggalAkhir'
        ));
    }

    /**
     * Quick Cetak Laporan (tanpa simpan)
     */
    public function cetakQuickLaporan(Request $request)
    {
        $periode = $request->get('periode', 'hari-ini');
        $tanggalAwal = $request->get('tanggal_awal', Carbon::today()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::today()->format('Y-m-d'));

        $query = $this->getFilteredQuery($periode, $tanggalAwal, $tanggalAkhir);

        $data = [
            'statistik' => $this->getStatistikUtama($query, $periode, $tanggalAwal, $tanggalAkhir),
            'produkTerlaris' => $this->getProdukTerlaris($query)->toArray(),
            'performansiKasir' => $this->getPerformansiKasir($query)->toArray(),
            'periode' => $this->getLabelPeriode($periode, $tanggalAwal, $tanggalAkhir),
            'tanggalCetak' => Carbon::now()->translatedFormat('l, d F Y H:i:s'),
            'analisisKeuangan' => [
                'pertumbuhan' => 0,
                'metode_pembayaran' => []
            ],
            'trenPenjualan' => []
        ];

        $pdf = Pdf::loadView('admin.laporan.cetak-laporan', $data);

        $filename = 'laporan-quick-' . Carbon::now()->format('Y-m-d-H-i') . '.pdf';
        return $pdf->download($filename);
    }

    // ==================== METHOD PRIVATE ====================

    private function getFilteredQuery($periode, $tanggalAwal, $tanggalAkhir)
    {
        $query = Transaksi::where('status', 'Selesai');

        switch ($periode) {
            case 'hari-ini':
                $query->whereDate('tanggal_transaksi', Carbon::today());
                break;
            case 'kemarin':
                $query->whereDate('tanggal_transaksi', Carbon::yesterday());
                break;
            case 'minggu-ini':
                $query->whereBetween('tanggal_transaksi', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'bulan-ini':
                $query->whereBetween('tanggal_transaksi', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
                break;
            case 'custom':
                $query->whereBetween('tanggal_transaksi', [
                    Carbon::parse($tanggalAwal)->startOfDay(),
                    Carbon::parse($tanggalAkhir)->endOfDay()
                ]);
                break;
        }

        return $query;
    }

    private function getStatistikUtama($query, $periode = null, $tanggalAwal = null, $tanggalAkhir = null)
    {
        $totalPenjualan = $query->sum('total_bayar') ?? 0;
        $totalTransaksi = $query->count();

        $transaksiTunai = $query->where('metode_pembayaran', 'Tunai')->count();
        $transaksiNonTunai = $totalTransaksi - $transaksiTunai;

        // Hitung profit
        $profit = 0;
        $transaksiIds = $query->pluck('transaksi_id')->toArray();
        
        if (!empty($transaksiIds)) {
            $profitData = DB::table('transaksi_items')
                ->join('transaksi', 'transaksi_items.transaksi_id', '=', 'transaksi.transaksi_id')
                ->join('produk', 'transaksi_items.produk_id', '=', 'produk.produk_id')
                ->whereIn('transaksi.transaksi_id', $transaksiIds)
                ->select(DB::raw('SUM((transaksi_items.harga_jual - COALESCE(produk.harga_beli, 0)) * transaksi_items.qty) as total_profit'))
                ->first();

            $profit = $profitData->total_profit ?? 0;
        }

        // Total pengeluaran dihapus - set ke 0
        $pengeluaran = 0;

        return [
            'total_penjualan' => $totalPenjualan,
            'total_transaksi' => $totalTransaksi,
            'transaksi_tunai' => $transaksiTunai,
            'transaksi_non_tunai' => $transaksiNonTunai,
            'rata_rata_transaksi' => $totalTransaksi > 0 ? round($totalPenjualan / $totalTransaksi) : 0,
            'total_profit' => $profit,
            'total_pengeluaran' => $pengeluaran,
            'net_profit' => $profit, // Net profit sama dengan total profit karena tidak ada pengeluaran
            'periode_text' => $this->getLabelPeriode($periode, $tanggalAwal, $tanggalAkhir)
        ];
    }

    private function getProdukTerlaris($query)
    {
        $transaksiIds = $query->pluck('transaksi_id')->toArray();
        
        if (empty($transaksiIds)) {
            return collect();
        }

        $produkTerlaris = TransaksiItem::join('transaksi', 'transaksi_items.transaksi_id', '=', 'transaksi.transaksi_id')
            ->join('produk', 'transaksi_items.produk_id', '=', 'produk.produk_id')
            ->whereIn('transaksi.transaksi_id', $transaksiIds)
            ->select(
                'produk.nama_produk',
                'produk.harga_jual',
                'produk.harga_beli',
                DB::raw('SUM(transaksi_items.qty) as total_terjual'),
                DB::raw('SUM(transaksi_items.qty * transaksi_items.harga_jual) as total_pendapatan')
            )
            ->groupBy('produk.produk_id', 'produk.nama_produk', 'produk.harga_jual', 'produk.harga_beli')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return $produkTerlaris;
    }

    private function getPerformansiKasir($query)
    {
        $transaksiIds = $query->pluck('transaksi_id')->toArray();
        
        if (empty($transaksiIds)) {
            return collect();
        }

        $performansi = User::join('transaksi', 'users.user_id', '=', 'transaksi.kasir_id')
            ->whereIn('transaksi.transaksi_id', $transaksiIds)
            ->select(
                'users.user_id',
                'users.nama_lengkap',
                DB::raw('COUNT(transaksi.transaksi_id) as total_transaksi'),
                DB::raw('SUM(transaksi.total_bayar) as total_penjualan')
            )
            ->groupBy('users.user_id', 'users.nama_lengkap')
            ->orderByDesc('total_penjualan')
            ->get();

        return $performansi;
    }

    private function getLabelPeriode($periode, $tanggalAwal, $tanggalAkhir)
    {
        switch ($periode) {
            case 'hari-ini':
                return 'Hari Ini (' . Carbon::today()->format('d/m/Y') . ')';
            case 'kemarin':
                return 'Kemarin (' . Carbon::yesterday()->format('d/m/Y') . ')';
            case 'minggu-ini':
                return 'Minggu Ini (' . Carbon::now()->startOfWeek()->format('d/m') . ' - ' . Carbon::now()->endOfWeek()->format('d/m/Y') . ')';
            case 'bulan-ini':
                return 'Bulan Ini (' . Carbon::now()->format('F Y') . ')';
            case 'custom':
                return 'Periode ' . Carbon::parse($tanggalAwal)->format('d/m/Y') . ' - ' . Carbon::parse($tanggalAkhir)->format('d/m/Y');
            default:
                return 'Hari Ini';
        }
    }
}