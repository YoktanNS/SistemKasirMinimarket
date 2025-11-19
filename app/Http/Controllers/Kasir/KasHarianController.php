<?php
// app/Http\Controllers\Kasir\KasHarianController.php
namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\KasHarian;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF facade

class KasHarianController extends Controller
{
    public function index()
    {
        $kasHarian = KasHarian::where('tanggal', today())->first();
        
        if (!$kasHarian) {
            return view('kasir.buka-kas');
        }
        
        // PERBAIKAN: Update data kas dari transaksi menggunakan method baru
        $this->updateKasFromTransaksi($kasHarian);
        
        // Data untuk dashboard
        $stats = [
            'total_transaksi' => Transaksi::whereDate('tanggal_transaksi', today())
                ->where('status', 'Selesai')->count(),
            'total_penjualan' => Transaksi::whereDate('tanggal_transaksi', today())
                ->where('status', 'Selesai')->sum('total_bayar') ?? 0,
            'transaksi_tunai' => Transaksi::whereDate('tanggal_transaksi', today())
                ->where('status', 'Selesai')
                ->where('metode_pembayaran', 'Tunai')->count(),
            'total_pengeluaran' => Pengeluaran::whereDate('tanggal', today())->sum('jumlah') ?? 0
        ];
        
        $transaksiTerbaru = Transaksi::with('items')
            ->whereDate('tanggal_transaksi', today())
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(10)
            ->get();
            
        $pengeluaranHariIni = Pengeluaran::whereDate('tanggal', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('kasir.dashboard', compact(
            'kasHarian', 'stats', 'transaksiTerbaru', 'pengeluaranHariIni'
        ));
    }
    
    /**
     * METHOD BARU: Update data kas dari transaksi
     */
    public function updateKasFromTransaksi($kasHarian)
    {
        $today = Carbon::today();
        
        try {
            // Hitung penerimaan tunai dari transaksi tunai
            $penerimaanTunai = Transaksi::whereDate('tanggal_transaksi', $today)
                ->where('status', 'Selesai')
                ->where('metode_pembayaran', 'Tunai')
                ->sum('total_bayar') ?? 0;
                
            // Hitung penerimaan non-tunai dari transaksi non-tunai
            $penerimaanNonTunai = Transaksi::whereDate('tanggal_transaksi', $today)
                ->where('status', 'Selesai')
                ->where('metode_pembayaran', '!=', 'Tunai')
                ->sum('total_bayar') ?? 0;
                
            // Hitung total pengeluaran
            $totalPengeluaran = Pengeluaran::whereDate('tanggal', $today)
                ->sum('jumlah') ?? 0;
                
            // Hitung saldo akhir (saldo awal + penerimaan tunai - pengeluaran)
            $saldoAkhir = $kasHarian->saldo_awal + $penerimaanTunai - $totalPengeluaran;
            
            // Update kas harian
            $kasHarian->update([
                'penerimaan_tunai' => $penerimaanTunai,
                'penerimaan_non_tunai' => $penerimaanNonTunai,
                'pengeluaran' => $totalPengeluaran,
                'saldo_akhir' => $saldoAkhir
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error update kas from transaksi: ' . $e->getMessage());
        }
    }
    
    /**
     * BUKA KAS HARIAN - DIPERBAIKI: Fleksibel saldo awal
     */
    public function bukaKas(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'saldo_awal' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string|max:255'
            ]);
            
            // Cek apakah sudah ada kas hari ini
            $existingKas = KasHarian::where('tanggal', today())->first();
            if ($existingKas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kas untuk hari ini sudah dibuka'
                ], 400);
            }
            
            // PERBAIKAN: Ambil saldo kemarin hanya sebagai referensi
            $saldoKemarin = KasHarian::where('tanggal', '<', today())
                ->orderBy('tanggal', 'desc')
                ->first();
            
            $saldoAwalKemarin = $saldoKemarin ? $saldoKemarin->saldo_akhir : 0;
            $saldoAwalHariIni = $request->saldo_awal;
            
            // Buat keterangan yang informatif
            $keterangan = $request->keterangan;
            if (!$keterangan) {
                $selisih = $saldoAwalHariIni - $saldoAwalKemarin;
                if ($selisih > 0) {
                    $keterangan = "Kas dibuka - Tambah modal: Rp " . number_format($selisih, 0, ',', '.');
                } elseif ($selisih < 0) {
                    $keterangan = "Kas dibuka - Kurang modal: Rp " . number_format(abs($selisih), 0, ',', '.');
                } else {
                    $keterangan = "Kas dibuka - Saldo sama dengan kemarin";
                }
            }

            $kasHarian = KasHarian::create([
                'tanggal' => today(),
                'saldo_awal' => $saldoAwalHariIni,
                'penerimaan_tunai' => 0,
                'penerimaan_non_tunai' => 0,
                'pengeluaran' => 0,
                'saldo_akhir' => $saldoAwalHariIni,
                'status' => 'Open',
                'user_id' => auth()->id(),
                'keterangan' => $keterangan
            ]);
            
            // Log untuk audit
            \Log::info('Kas dibuka dengan saldo fleksibel', [
                'tanggal' => today(),
                'saldo_kemarin' => $saldoAwalKemarin,
                'saldo_hari_ini' => $saldoAwalHariIni,
                'selisih' => $saldoAwalHariIni - $saldoAwalKemarin,
                'user' => auth()->user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kas berhasil dibuka',
                'data' => [
                    'saldo_kemarin' => $saldoAwalKemarin,
                    'saldo_hari_ini' => $saldoAwalHariIni,
                    'selisih' => $saldoAwalHariIni - $saldoAwalKemarin
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error buka kas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuka kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tutup kas harian
     */
    public function tutupKas(Request $request)
    {
        try {
            DB::beginTransaction();

            $today = Carbon::today();
            
            // Cari kas harian yang open
            $kasHarian = KasHarian::where('tanggal', $today)
                ->where('status', 'Open')
                ->first();

            if (!$kasHarian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kas yang terbuka untuk hari ini'
                ], 404);
            }

            // Update final data sebelum tutup
            $this->updateKasFromTransaksi($kasHarian);
            $kasHarian->refresh(); // Reload data terbaru

            // Update status kas menjadi Closed
            $kasHarian->update([
                'status' => 'Closed',
                'keterangan_tutup' => $request->keterangan ?? 'Kas ditutup',
                'waktu_tutup' => Carbon::now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kas berhasil ditutup',
                'kas_harian' => $kasHarian
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error tutup kas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menutup kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh data kas
     */
    public function refresh()
    {
        $today = Carbon::today();
        $kasHarian = KasHarian::where('tanggal', $today)->first();

        if ($kasHarian && $kasHarian->status === 'Open') {
            // Update data dari transaksi menggunakan method baru
            $this->updateKasFromTransaksi($kasHarian);
            $kasHarian->refresh(); // Reload data terbaru
        }

        return response()->json([
            'success' => true,
            'kas_harian' => $kasHarian
        ]);
    }

    /**
     * Cek status kas - DIPERBAIKI: Tambah info saldo kemarin
     */
    public function cekStatus()
    {
        $today = Carbon::today();
        $kasHarian = KasHarian::where('tanggal', $today)->first();

        // PERBAIKAN: Ambil saldo kemarin untuk referensi
        $saldoKemarin = KasHarian::where('tanggal', '<', $today)
            ->orderBy('tanggal', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'kas_harian' => $kasHarian,
            'is_open' => $kasHarian && $kasHarian->status === 'Open',
            'saldo_kemarin' => $saldoKemarin ? $saldoKemarin->saldo_akhir : 0
        ]);
    }

    /**
     * Tambah pengeluaran
     */
    public function tambahPengeluaran(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'keterangan' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:1',
                'kategori' => 'nullable|string|max:100'
            ]);
            
            $today = Carbon::today();
            
            // Cek apakah kas terbuka
            $kasHarian = KasHarian::where('tanggal', $today)
                ->where('status', 'Open')
                ->first();
                
            if (!$kasHarian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kas harus dibuka terlebih dahulu sebelum menambah pengeluaran'
                ], 400);
            }

            $pengeluaran = Pengeluaran::create([
                'tanggal' => $today,
                'keterangan' => $request->keterangan,
                'jumlah' => $request->jumlah,
                'kategori' => $request->kategori ?? 'Lainnya',
                'user_id' => auth()->id()
            ]);

            // Update kas harian setelah menambah pengeluaran
            $this->updateKasFromTransaksi($kasHarian);

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil ditambahkan',
                'pengeluaran' => $pengeluaran
            ]);

        } catch (\Exception $e) {
            \Log::error('Error tambah pengeluaran: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pengeluaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Laporan kas harian
     */
    public function laporan($id = null)
    {
        $tanggal = $id ? Carbon::parse($id) : Carbon::today();
        
        $kasHarian = KasHarian::where('tanggal', $tanggal)->first();
        
        if (!$kasHarian) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data kas untuk tanggal tersebut'
            ], 404);
        }
        
        $transaksi = Transaksi::with('items')
            ->whereDate('tanggal_transaksi', $tanggal)
            ->where('status', 'Selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();
            
        $pengeluaran = Pengeluaran::whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'kas_harian' => $kasHarian,
            'transaksi' => $transaksi,
            'pengeluaran' => $pengeluaran
        ]);
    }

    /**
     * CETAK LAPORAN - DENGAN PDF
     */
    public function cetakLaporanHarian(Request $request, $id = null)
    {
        try {
            $tanggal = $id ? Carbon::parse($id) : Carbon::today();
            
            $kasHarian = KasHarian::where('tanggal', $tanggal)->first();
            
            if (!$kasHarian) {
                return redirect()->back()->with('error', 'Tidak ada data kas untuk tanggal tersebut');
            }

            // Ambil data transaksi
            $transaksi = Transaksi::with('items')
                ->whereDate('tanggal_transaksi', $tanggal)
                ->where('status', 'Selesai')
                ->orderBy('tanggal_transaksi', 'desc')
                ->get();

            // Ambil data pengeluaran
            $pengeluaran = Pengeluaran::whereDate('tanggal', $tanggal)
                ->orderBy('created_at', 'desc')
                ->get();

            // Hitung statistik
            $totalTransaksi = $transaksi->count();
            $totalPenjualan = $transaksi->sum('total_bayar');
            $totalItemTerjual = $transaksi->sum(function($trx) {
                return $trx->items->sum('jumlah');
            });
            $rataRataTransaksi = $totalTransaksi > 0 ? $totalPenjualan / $totalTransaksi : 0;

            // Hitung metode pembayaran
            $metodePembayaran = $transaksi->groupBy('metode_pembayaran')->map(function($group, $metode) {
                return (object) [
                    'metode_pembayaran' => $metode,
                    'total' => $group->count(),
                    'jumlah' => $group->sum('total_bayar')
                ];
            })->values();

            // Hitung total penerimaan
            $totalPenerimaan = $kasHarian->penerimaan_tunai + $kasHarian->penerimaan_non_tunai;

            $data = [
                'kasHarian' => $kasHarian,
                'transaksi' => $transaksi,
                'pengeluaran' => $pengeluaran,
                'tanggalLaporan' => Carbon::parse($tanggal)->translatedFormat('l, d F Y'),
                'totalTransaksi' => $totalTransaksi,
                'totalPenjualan' => $totalPenjualan,
                'totalItemTerjual' => $totalItemTerjual,
                'rataRataTransaksi' => $rataRataTransaksi,
                'metodePembayaran' => $metodePembayaran,
                'totalPenerimaan' => $totalPenerimaan
            ];

            // Generate PDF dengan DomPDF
            $pdf = Pdf::loadView('kasir.laporan.cetak-laporan-harian', $data);
            
            return $pdf->download('laporan-kas-' . $tanggal->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error cetak laporan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencetak laporan: ' . $e->getMessage());
        }
    }

    /**
     * Reset kas hari ini (untuk development)
     */
    public function resetKasHariIni()
    {
        try {
            $today = Carbon::today();
            $kasHarian = KasHarian::where('tanggal', $today)->first();
            
            if ($kasHarian) {
                $kasHarian->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Kas hari ini berhasil direset'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error reset kas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset kas: ' . $e->getMessage()
            ], 500);
        }
    }
}