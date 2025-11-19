<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\KasHarian;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * DASHBOARD SEDERHANA
     */
    public function dashboardKas()
    {
        $today = Carbon::today();
        
        // Cek kas harian hari ini
        $kasHarian = KasHarian::where('tanggal', $today)->first();

        // Jika kas belum dibuka, tetap tampilkan dashboard tapi dengan pesan
        if (!$kasHarian) {
            return view('kasir.dashboard', [
                'kasHarian' => null,
                'stats' => $this->getEmptyStats(),
                'transaksiTerbaru' => collect(),
            ]);
        }

        // Update data kas dari transaksi jika status Open
        if ($kasHarian->status == 'Open') {
            $this->updateKasFromTransaksi($kasHarian);
            $kasHarian->refresh();
        }

        // Data statistik sederhana
        $stats = $this->getStats($today, $kasHarian);
        $transaksiTerbaru = $this->getTransaksiTerbaru($today);

        return view('kasir.dashboard', compact(
            'kasHarian',
            'stats',
            'transaksiTerbaru'
        ));
    }

    /**
     * LAPORAN HARIAN - DIPERBAIKI: Hapus pengeluaran
     */
    public function laporanHarian()
    {
        $today = Carbon::today();
        
        // Data untuk laporan harian
        $kasHarian = KasHarian::where('tanggal', $today)->first();
        
        // Transaksi hari ini dengan relasi
        $transaksiHariIni = Transaksi::with(['kasir', 'items'])
            ->whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Hitung statistik
        $totalTransaksi = $transaksiHariIni->count();
        $totalPenjualan = $transaksiHariIni->sum('total_bayar');
        $totalItemTerjual = $transaksiHariIni->sum('total_item');
        $rataRataTransaksi = $totalTransaksi > 0 ? round($totalPenjualan / $totalTransaksi) : 0;

        // Metode pembayaran
        $metodePembayaran = Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->select('metode_pembayaran', DB::raw('COUNT(*) as total'), DB::raw('SUM(total_bayar) as jumlah'))
            ->groupBy('metode_pembayaran')
            ->get();

        return view('kasir.laporan-harian', compact(
            'kasHarian',
            'transaksiHariIni',
            'totalTransaksi',
            'totalPenjualan',
            'totalItemTerjual',
            'rataRataTransaksi',
            'metodePembayaran'
        ));
    }

    /**
     * UPDATE KAS DARI TRANSAKSI
     */
    private function updateKasFromTransaksi($kasHarian)
    {
        $today = Carbon::today();

        // Hitung penerimaan tunai hari ini
        $penerimaanTunai = Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->where('metode_pembayaran', 'Tunai')
            ->sum('total_bayar') ?? 0;

        // Hitung penerimaan non-tunai
        $penerimaanNonTunai = Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->where('metode_pembayaran', '!=', 'Tunai')
            ->sum('total_bayar') ?? 0;

        // Hitung total penerimaan
        $totalPenerimaan = $penerimaanTunai + $penerimaanNonTunai;

        // Hitung saldo akhir
        $saldoAkhir = $kasHarian->saldo_awal + $totalPenerimaan;

        // Update kas harian
        $kasHarian->update([
            'penerimaan_tunai' => $penerimaanTunai,
            'penerimaan_non_tunai' => $penerimaanNonTunai,
            'total_penerimaan' => $totalPenerimaan,
            'saldo_akhir' => $saldoAkhir
        ]);
    }

    /**
     * GET STATS - Data statistik sederhana
     */
    private function getStats($today, $kasHarian)
    {
        // Total transaksi dan penjualan
        $transaksiHariIni = Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->get();

        $totalTransaksi = $transaksiHariIni->count();
        $totalPenjualan = $transaksiHariIni->sum('total_bayar') ?? 0;

        // Transaksi tunai
        $transaksiTunai = Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->where('metode_pembayaran', 'Tunai')
            ->count();

        return [
            'total_transaksi' => $totalTransaksi,
            'total_penjualan' => $totalPenjualan,
            'transaksi_tunai' => $transaksiTunai,
            'rata_rata_transaksi' => $totalTransaksi > 0 ? round($totalPenjualan / $totalTransaksi) : 0,
        ];
    }

    /**
     * GET TRANSAKSI TERBARU - 5 transaksi terbaru
     */
    private function getTransaksiTerbaru($today)
    {
        return Transaksi::with(['items'])
            ->whereDate('tanggal_transaksi', $today)
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * GET EMPTY STATS - Untuk fallback
     */
    private function getEmptyStats()
    {
        return [
            'total_transaksi' => 0,
            'total_penjualan' => 0,
            'transaksi_tunai' => 0,
            'rata_rata_transaksi' => 0,
        ];
    }
}