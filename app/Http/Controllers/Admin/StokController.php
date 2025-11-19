<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Stok;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index()
    {
        $stok = Stok::with(['produk', 'supplier'])
                    ->orderByDesc('created_at')
                    ->paginate(15);
        $produk = Produk::orderBy('nama_produk')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('admin.stok.index', compact('stok', 'produk', 'suppliers'));
    }

    /**
     * Method utama untuk handle kedua jenis transaksi stok - SIMPLIFIED
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'jumlah' => 'required|integer|min:1',
            'supplier_id' => 'required_if:jenis_transaksi,Masuk|exists:supplier,supplier_id',
            'keterangan' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $produk = Produk::findOrFail($request->produk_id);
            $stokSebelum = $produk->stok_tersedia;
            
            // Hitung stok sesudah berdasarkan jenis transaksi
            if ($request->jenis_transaksi == 'Masuk') {
                $stokSesudah = $stokSebelum + $request->jumlah;
            } else {
                // Validasi stok cukup untuk keluar
                if ($stokSebelum < $request->jumlah) {
                    return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $stokSebelum);
                }
                $stokSesudah = $stokSebelum - $request->jumlah;
            }

            // Buat record stok - SIMPLIFIED
            Stok::create([
                'produk_id' => $produk->produk_id,
                'supplier_id' => $request->jenis_transaksi == 'Masuk' ? $request->supplier_id : null,
                'jenis_transaksi' => $request->jenis_transaksi,
                'jumlah' => $request->jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'tanggal_transaksi' => now(),
                'user_id' => Auth::id(),
                'keterangan' => $request->keterangan,
            ]);

            // Update stok produk
            $produk->update(['stok_tersedia' => $stokSesudah]);

            DB::commit();

            return back()->with('success', 'Transaksi stok ' . strtolower($request->jenis_transaksi) . ' berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk stok masuk - SIMPLIFIED
     */
    public function masuk(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'supplier_id' => 'required|exists:supplier,supplier_id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $produk = Produk::findOrFail($request->produk_id);
            $stokSebelum = $produk->stok_tersedia;
            $stokSesudah = $stokSebelum + $request->jumlah;

            Stok::create([
                'produk_id' => $produk->produk_id,
                'supplier_id' => $request->supplier_id,
                'jenis_transaksi' => 'Masuk',
                'jumlah' => $request->jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'tanggal_transaksi' => now(),
                'user_id' => Auth::id(),
                'keterangan' => $request->keterangan,
            ]);

            $produk->update(['stok_tersedia' => $stokSesudah]);

            DB::commit();

            return back()->with('success', 'Stok masuk berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk stok keluar - SIMPLIFIED
     */
    public function keluar(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $produk = Produk::findOrFail($request->produk_id);
            $stokSebelum = $produk->stok_tersedia;

            // Validasi stok cukup
            if ($stokSebelum < $request->jumlah) {
                return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $stokSebelum);
            }

            $stokSesudah = $stokSebelum - $request->jumlah;

            Stok::create([
                'produk_id' => $produk->produk_id,
                'jenis_transaksi' => 'Keluar',
                'jumlah' => $request->jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'tanggal_transaksi' => now(),
                'user_id' => Auth::id(),
                'keterangan' => $request->keterangan,
            ]);

            $produk->update(['stok_tersedia' => $stokSesudah]);

            DB::commit();

            return back()->with('success', 'Stok keluar berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}