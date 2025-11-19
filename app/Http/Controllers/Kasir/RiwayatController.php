<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Produk;
use App\Models\KasHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatController extends Controller
{
    /**
     * RIWAYAT TRANSAKSI - Dengan filter & pencarian
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['kasir', 'items'])
            ->orderBy('tanggal_transaksi', 'desc');

        // Filter by tanggal
        if ($request->has('tanggal') && $request->tanggal) {
            $query->whereDate('tanggal_transaksi', $request->tanggal);
        }

        // Filter by no transaksi
        if ($request->has('search') && $request->search) {
            $query->where('no_transaksi', 'LIKE', "%{$request->search}%");
        }

        // Filter by metode pembayaran
        if ($request->has('metode_pembayaran') && $request->metode_pembayaran) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        $transaksi = $query->paginate(20);

        return view('kasir.riwayat', compact('transaksi'));
    }

    /**
     * DETAIL TRANSAKSI
     */
    public function show($id)
    {
        $transaksi = Transaksi::with(['items.produk', 'kasir'])
            ->findOrFail($id);

        return view('kasir.detail-transaksi', compact('transaksi'));
    }

    /**
     * BATALKAN TRANSAKSI
     */
    public function batalkanTransaksi($id)
    {
        DB::beginTransaction();

        try {
            $transaksi = Transaksi::findOrFail($id);

            if (!$transaksi->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak dapat dibatalkan (max 24 jam)'
                ], 400);
            }

            // Kembalikan stok produk
            foreach ($transaksi->items as $item) {
                $produk = Produk::find($item->produk_id);
                $produk->increment('stok_tersedia', $item->qty);
                
                // Update status produk jika sebelumnya habis
                if ($produk->status === 'Habis' && $produk->stok_tersedia > 0) {
                    $produk->update(['status' => 'Tersedia']);
                }
            }

            // Update kas harian
            $kasHarian = KasHarian::where('tanggal', $transaksi->tanggal_transaksi->format('Y-m-d'))->first();
            if ($kasHarian) {
                $kasHarian->decrement('penerimaan', $transaksi->total_bayar);
                $kasHarian->saldo_akhir = $kasHarian->saldo_awal + $kasHarian->penerimaan - $kasHarian->pengeluaran;
                $kasHarian->save();
            }

            // Update status transaksi
            $transaksi->update(['status' => 'Dibatalkan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * EXPORT RIWAYAT TRANSAKSI
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $transaksi = Transaksi::with(['items', 'kasir'])
            ->whereBetween('tanggal_transaksi', [$request->start_date, $request->end_date])
            ->selesai()
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // TODO: Implement export to PDF/Excel
        return response()->json([
            'success' => true,
            'data' => $transaksi,
            'message' => 'Export feature coming soon'
        ]);
    }

    /**
     * HAPUS TRANSAKSI - Hapus transaksi dan kembalikan stok
     */
    public function hapusTransaksi($id)
    {
        DB::beginTransaction();

        try {
            $transaksi = Transaksi::with('items')->findOrFail($id);

            // Kembalikan stok produk
            foreach ($transaksi->items as $item) {
                $produk = Produk::find($item->produk_id);
                if ($produk) {
                    $produk->increment('stok_tersedia', $item->qty);
                    
                    // Update status produk jika sebelumnya habis
                    if ($produk->status === 'Habis' && $produk->stok_tersedia > 0) {
                        $produk->update(['status' => 'Tersedia']);
                    }
                }
            }

            // Update kas harian (kurangi penerimaan)
            $kasHarian = KasHarian::where('tanggal', $transaksi->tanggal_transaksi->format('Y-m-d'))->first();
            if ($kasHarian) {
                $kasHarian->decrement('penerimaan', $transaksi->total_bayar);
                $kasHarian->saldo_akhir = $kasHarian->saldo_awal + $kasHarian->penerimaan - $kasHarian->pengeluaran;
                $kasHarian->save();
            }

            // Hapus transaksi items terlebih dahulu
            TransaksiItem::where('transaksi_id', $id)->delete();

            // Hapus transaksi
            $transaksi->delete();

            DB::commit();

            return redirect()->route('kasir.riwayat')
                ->with('success', 'Transaksi berhasil dihapus dan stok produk dikembalikan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hapus Transaksi Error: ' . $e->getMessage());
            
            return redirect()->route('kasir.riwayat')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * HAPUS MULTIPLE TRANSAKSI - Hapus beberapa transaksi sekaligus
     */
    public function hapusMultipleTransaksi(Request $request)
    {
        $request->validate([
            'transaksi_ids' => 'required'
        ]);

        // Parse JSON string to array
        $transaksiIds = json_decode($request->transaksi_ids, true);

        if (empty($transaksiIds)) {
            return redirect()->route('kasir.riwayat')
                ->with('error', 'Tidak ada transaksi yang dipilih');
        }

        DB::beginTransaction();

        try {
            $deletedCount = 0;

            foreach ($transaksiIds as $transaksi_id) {
                $transaksi = Transaksi::with('items')->find($transaksi_id);

                if ($transaksi) {
                    // Kembalikan stok produk
                    foreach ($transaksi->items as $item) {
                        $produk = Produk::find($item->produk_id);
                        if ($produk) {
                            $produk->increment('stok_tersedia', $item->qty);
                            
                            if ($produk->status === 'Habis' && $produk->stok_tersedia > 0) {
                                $produk->update(['status' => 'Tersedia']);
                            }
                        }
                    }

                    // Update kas harian
                    $kasHarian = KasHarian::where('tanggal', $transaksi->tanggal_transaksi->format('Y-m-d'))->first();
                    if ($kasHarian) {
                        $kasHarian->decrement('penerimaan', $transaksi->total_bayar);
                        $kasHarian->saldo_akhir = $kasHarian->saldo_awal + $kasHarian->penerimaan - $kasHarian->pengeluaran;
                        $kasHarian->save();
                    }

                    // Hapus transaksi items
                    TransaksiItem::where('transaksi_id', $transaksi_id)->delete();

                    // Hapus transaksi
                    $transaksi->delete();
                    $deletedCount++;
                }
            }

            DB::commit();

            return redirect()->route('kasir.riwayat')
                ->with('success', $deletedCount . ' transaksi berhasil dihapus dan stok produk dikembalikan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hapus Multiple Transaksi Error: ' . $e->getMessage());
            
            return redirect()->route('kasir.riwayat')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * GET TRANSAKSI DETAIL FOR MODAL (AJAX)
     */
    public function getTransaksiDetail($id)
    {
        try {
            $transaksi = Transaksi::with(['items.produk', 'kasir'])
                ->findOrFail($id);

            $html = view('kasir.partials.transaksi-detail', compact('transaksi'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }
}