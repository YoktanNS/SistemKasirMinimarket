<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\TransaksiItem;
use App\Models\Transaksi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiItemController extends Controller
{
    /**
     * GET ITEMS BY TRANSAKSI ID - Untuk lihat detail items
     */
    public function getByTransaksi($transaksiId)
    {
        $items = TransaksiItem::with('produk')
            ->where('transaksi_id', $transaksiId)
            ->get();

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * GET TOP PRODUCTS - Produk terlaris
     */
    public function getTopProducts(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month
        
        $query = TransaksiItem::select(
            'produk_id',
            'nama_produk',
            DB::raw('SUM(qty) as total_terjual'),
            DB::raw('SUM(subtotal) as total_pendapatan')
        );

        // Filter by period - PERBAIKAN: pakai whereHas dengan kondisi yang benar
        if ($period === 'today') {
            $query->whereHas('transaksi', function($q) {
                $q->whereDate('tanggal_transaksi', today())
                  ->where('status', 'Selesai'); // ← TAMBAHKAN INI
            });
        } elseif ($period === 'week') {
            $query->whereHas('transaksi', function($q) {
                $q->whereBetween('tanggal_transaksi', [now()->startOfWeek(), now()->endOfWeek()])
                  ->where('status', 'Selesai'); // ← TAMBAHKAN INI
            });
        } elseif ($period === 'month') {
            $query->whereHas('transaksi', function($q) {
                $q->whereMonth('tanggal_transaksi', now()->month)
                  ->where('status', 'Selesai'); // ← TAMBAHKAN INI
            });
        }

        $topProducts = $query->groupBy('produk_id', 'nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'top_products' => $topProducts
        ]);
    }

    /**
     * GET SALES BY CATEGORY - Penjualan by kategori
     */
    public function getSalesByCategory(Request $request)
    {
        $period = $request->get('period', 'today');
        
        $salesByCategory = TransaksiItem::select(
            DB::raw('k.nama_kategori as kategori'),
            DB::raw('SUM(ti.qty) as total_terjual'),
            DB::raw('SUM(ti.subtotal) as total_pendapatan')
        )
        ->from('transaksi_items as ti')
        ->join('produk as p', 'ti.produk_id', '=', 'p.produk_id')
        ->join('kategori_produk as k', 'p.kategori_id', '=', 'k.kategori_id')
        ->whereHas('transaksi', function($query) use ($period) {
            $query->where('status', 'Selesai'); // ← PERBAIKAN: ganti selesai() dengan where
            
            if ($period === 'today') {
                $query->whereDate('tanggal_transaksi', today());
            } elseif ($period === 'week') {
                $query->whereBetween('tanggal_transaksi', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereMonth('tanggal_transaksi', now()->month);
            }
        })
        ->groupBy('k.nama_kategori')
        ->orderBy('total_pendapatan', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'sales_by_category' => $salesByCategory
        ]);
    }

    /**
     * GET DAILY SALES - Grafik penjualan harian - PERBAIKAN
     */
    public function getDailySales(Request $request)
    {
        $days = $request->get('days', 7); // Default 7 hari terakhir
        
        $dailySales = Transaksi::select(
            DB::raw('DATE(tanggal_transaksi) as tanggal'),
            DB::raw('COUNT(*) as total_transaksi'),
            DB::raw('SUM(total_bayar) as total_penjualan')
        )
        ->where('status', 'Selesai') // ← PERBAIKAN: ganti selesai() dengan where
        ->where('tanggal_transaksi', '>=', now()->subDays($days))
        ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
        ->orderBy('tanggal', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'daily_sales' => $dailySales
        ]);
    }

    /**
     * UPDATE ITEM QTY - Untuk edit quantity (jika ada kebutuhan)
     */
    public function updateQty(Request $request, $itemId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $item = TransaksiItem::findOrFail($itemId);
            $transaksi = $item->transaksi;

            // Cek apakah transaksi masih bisa diedit
            if (!$transaksi->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah tidak dapat diubah'
                ], 400);
            }

            $oldQty = $item->qty;
            $newQty = $request->qty;
            $difference = $newQty - $oldQty;

            // Update stok produk
            $produk = $item->produk;
            if ($difference > 0) {
                // Kurangi stok
                if ($produk->stok_tersedia < $difference) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi'
                    ], 400);
                }
                $produk->decrement('stok_tersedia', $difference);
            } else {
                // Tambah stok
                $produk->increment('stok_tersedia', abs($difference));
            }

            // Update item
            $item->update([
                'qty' => $newQty,
                'subtotal' => $newQty * $item->harga_jual
            ]);

            // Update transaksi totals
            $transaksi->update([
                'total_item' => $transaksi->items->sum('qty'),
                'subtotal' => $transaksi->items->sum('subtotal'),
                'total_bayar' => $transaksi->items->sum('subtotal') - $transaksi->diskon
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quantity berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal update quantity: ' . $e->getMessage()
            ], 500);
        }
    }
}