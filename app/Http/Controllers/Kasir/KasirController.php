<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\KasHarian;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class KasirController extends Controller
{
    /**
     * HALAMAN UTAMA KASIR - Input Transaksi
     */
    public function index()
    {
        $this->calculateCartTotal();

        return view('kasir.index', [
            'subtotal' => session('subtotal', 0),
            'diskon' => session('diskon', 0),
            'total_bayar' => session('total_bayar', 0)
        ]);
    }

    /**
     * LIHAT DAFTAR PRODUK (READ-ONLY) - Untuk kasir cek produk
     */
    public function daftarProduk(Request $request)
    {
        $query = Produk::with('kategori')
            ->where('status', 'Tersedia')
            ->orderBy('nama_produk', 'asc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', "%{$search}%")
                    ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('kategori_id') && $request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $produk = $query->paginate(20);
        $kategoris = KategoriProduk::all();

        // Debug info untuk gambar dengan path yang benar
        foreach ($produk as $item) {
            if ($item->gambar_produk) {
                $path = public_path('storage/' . $item->gambar_produk);
                $item->gambar_exists = file_exists($path);
            }
        }

        // Hitung statistik stok
        $totalProduk = Produk::where('status', 'Tersedia')->count();
        $totalStokTersedia = Produk::where('status', 'Tersedia')->sum('stok_tersedia');
        $stokMenipis = Produk::where('status', 'Tersedia')
            ->whereRaw('stok_tersedia <= stok_minimum')
            ->where('stok_tersedia', '>', 0)
            ->count();
        $stokHabis = Produk::where('status', 'Tersedia')
            ->where('stok_tersedia', 0)
            ->count();

        return view('kasir.daftar-produk', compact(
            'produk',
            'kategoris',
            'totalProduk',
            'totalStokTersedia',
            'stokMenipis',
            'stokHabis'
        ));
    }

    /**
     * GET LOW STOCK ALERT - Notifikasi stok menipis
     */
    public function getLowStockAlert()
    {
        $lowStockProducts = Produk::with('kategori')
            ->where('status', 'Tersedia')
            ->whereRaw('stok_tersedia <= stok_minimum')
            ->orderBy('stok_tersedia', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'low_stock_products' => $lowStockProducts
        ]);
    }

    /**
     * QUICK SEARCH - Pencarian sederhana tanpa JavaScript kompleks
     */
    public function quickSearch(Request $request)
    {
        $request->validate([
            'search' => 'required|string|max:100'
        ]);

        $search = trim($request->search);

        try {
            $produk = Produk::where('status', 'Tersedia')
                ->where(function ($query) use ($search) {
                    $query->where('barcode', 'LIKE', "%{$search}%")
                        ->orWhere('nama_produk', 'LIKE', "%{$search}%");
                })
                ->limit(10)
                ->get();

            if ($produk->count() > 0) {
                $formattedProduk = $produk->map(function ($item) {
                    return [
                        'produk_id' => $item->produk_id,
                        'barcode' => $item->barcode,
                        'nama_produk' => $item->nama_produk,
                        'harga_jual' => $item->harga_jual,
                        'stok_tersedia' => $item->stok_tersedia
                    ];
                });

                return redirect()->route('kasir.index')->with([
                    'search_results' => $formattedProduk,
                    'search_query' => $search
                ]);
            }

            return redirect()->route('kasir.index')->with('search_error', 'Produk "' . $search . '" tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Quick Search Error: ' . $e->getMessage());
            return redirect()->route('kasir.index')->with('search_error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * QUICK ADD TO CART - Untuk form pencarian utama
     */
    public function quickAddToCart(Request $request)
    {
        $request->validate([
            'search' => 'required|string|max:100'
        ]);

        $search = trim($request->search);

        try {
            $produk = Produk::where('status', 'Tersedia')
                ->where('stok_tersedia', '>', 0)
                ->where(function ($query) use ($search) {
                    $query->where('barcode', $search)
                        ->orWhere('nama_produk', 'LIKE', "%{$search}%");
                })
                ->first();

            if (!$produk) {
                return redirect()->route('kasir.index')->with('search_error', 'Produk "' . $search . '" tidak ditemukan atau stok habis');
            }

            if ($produk->stok_tersedia < 1) {
                return redirect()->route('kasir.index')->with('search_error', 'Stok "' . $produk->nama_produk . '" habis');
            }

            $cart = session()->get('cart', []);
            $existingIndex = null;

            foreach ($cart as $index => $item) {
                if ($item['produk_id'] == $produk->produk_id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                $cart[$existingIndex]['qty'] += 1;
                $cart[$existingIndex]['subtotal'] = $cart[$existingIndex]['qty'] * $cart[$existingIndex]['harga_jual'];
            } else {
                $cart[] = [
                    'produk_id' => $produk->produk_id,
                    'barcode' => $produk->barcode ?? '', // PASTIKAN BARCODE ADA, DEFAULT KE STRING KOSONG
                    'nama_produk' => $produk->nama_produk,
                    'harga_jual' => $produk->harga_jual,
                    'qty' => 1,
                    'subtotal' => $produk->harga_jual
                ];
            }

            session(['cart' => $cart]);
            $this->calculateCartTotal();

            return redirect()->route('kasir.index')->with('success', 'Produk "' . $produk->nama_produk . '" berhasil ditambahkan ke keranjang');
        } catch (\Exception $e) {
            Log::error('Quick Add to Cart Error: ' . $e->getMessage());
            return redirect()->route('kasir.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * ADD TO CART - Tambah ke keranjang dari hasil pencarian
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'qty' => 'required|integer|min:1'
        ]);

        try {
            $produk = Produk::findOrFail($request->produk_id);

            if ($produk->stok_tersedia < $request->qty) {
                return redirect()->route('kasir.index')->with('error', 'Stok "' . $produk->nama_produk . '" tidak mencukupi. Stok tersedia: ' . $produk->stok_tersedia);
            }

            $cart = session()->get('cart', []);
            $existingIndex = null;

            foreach ($cart as $index => $item) {
                if ($item['produk_id'] == $request->produk_id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                $cart[$existingIndex]['qty'] += $request->qty;
                $cart[$existingIndex]['subtotal'] = $cart[$existingIndex]['qty'] * $cart[$existingIndex]['harga_jual'];
            } else {
                $cart[] = [
                    'produk_id' => $produk->produk_id,
                    'barcode' => $produk->barcode ?? '', // PASTIKAN BARCODE ADA, DEFAULT KE STRING KOSONG
                    'nama_produk' => $produk->nama_produk,
                    'harga_jual' => $produk->harga_jual,
                    'qty' => $request->qty,
                    'subtotal' => $request->qty * $produk->harga_jual
                ];
            }

            session(['cart' => $cart]);
            $this->calculateCartTotal();

            return redirect()->route('kasir.index')->with('success', 'Produk "' . $produk->nama_produk . '" berhasil ditambahkan ke keranjang');
        } catch (\Exception $e) {
            Log::error('Add to Cart Error: ' . $e->getMessage());
            return redirect()->route('kasir.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * UPDATE CART ITEM - Update quantity
     */
    public function updateCartItem(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'action' => 'required|in:increase,decrease'
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$request->index])) {
            return redirect()->route('kasir.index')->with('error', 'Item tidak ditemukan');
        }

        $item = &$cart[$request->index];
        $produk = Produk::find($item['produk_id']);

        if (!$produk) {
            return redirect()->route('kasir.index')->with('error', 'Produk tidak ditemukan');
        }

        if ($request->action === 'increase') {
            if ($item['qty'] < $produk->stok_tersedia) {
                $item['qty']++;
            } else {
                return redirect()->route('kasir.index')->with('error', 'Stok "' . $produk->nama_produk . '" tidak mencukupi. Stok tersedia: ' . $produk->stok_tersedia);
            }
        } else {
            if ($item['qty'] > 1) {
                $item['qty']--;
            } else {
                unset($cart[$request->index]);
                $cart = array_values($cart);
            }
        }

        if (isset($item['qty'])) {
            $item['subtotal'] = $item['qty'] * $item['harga_jual'];
        }

        session(['cart' => $cart]);
        $this->calculateCartTotal();

        return redirect()->route('kasir.index');
    }

    /**
     * REMOVE FROM CART - Hapus dari keranjang
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'index' => 'required|integer'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->index])) {
            $removedProduct = $cart[$request->index]['nama_produk'];
            unset($cart[$request->index]);
            $cart = array_values($cart);
            session(['cart' => $cart]);
            $this->calculateCartTotal();
            return redirect()->route('kasir.index')->with('success', 'Produk "' . $removedProduct . '" dihapus dari keranjang');
        }

        return redirect()->route('kasir.index')->with('error', 'Item tidak ditemukan');
    }

    /**
     * APPLY DISKON - Terapkan diskon
     */
    public function applyDiskon(Request $request)
    {
        $request->validate([
            'diskon' => 'required|numeric|min:0'
        ]);

        $subtotal = session('subtotal', 0);
        $diskon = $request->diskon;

        if ($diskon > $subtotal) {
            return redirect()->route('kasir.index')->with('error', 'Diskon tidak boleh lebih besar dari subtotal');
        }

        session(['diskon' => $diskon]);
        $this->calculateCartTotal();

        return redirect()->route('kasir.index')->with('success', 'Diskon berhasil diterapkan');
    }

    /**
     * RESET TRANSAKSI - Reset semua
     */
    public function resetTransaksi()
    {
        session()->forget(['cart', 'subtotal', 'diskon', 'total_bayar', 'search_results', 'search_error', 'search_query']);
        return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil direset');
    }

    /**
     * CALCULATE CART TOTAL - Hitung total
     */
    private function calculateCartTotal()
    {
        $cart = session()->get('cart', []);
        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $diskon = session()->get('diskon', 0);
        $total_bayar = max(0, $subtotal - $diskon);

        session([
            'subtotal' => $subtotal,
            'total_bayar' => $total_bayar
        ]);
    }

    /**
     * PROSES TRANSAKSI - Simpan transaksi ke database (DIPERBAIKI)
     */
    public function prosesTransaksi(Request $request)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:Tunai,Debit,QRIS,Transfer',
            'jumlah_uang' => 'required|numeric|min:0'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('kasir.index')->with('error', 'Keranjang belanja kosong');
        }

        $subtotal = session('subtotal', 0);
        $diskon = session('diskon', 0);
        $total_bayar = session('total_bayar', 0);
        $jumlah_uang = $request->jumlah_uang;

        // Validasi uang untuk tunai
        if ($request->metode_pembayaran === 'Tunai' && $jumlah_uang < $total_bayar) {
            return redirect()->route('kasir.index')->with(
                'error',
                'Jumlah uang tidak mencukupi. Total: Rp ' . number_format($total_bayar, 0, ',', '.')
            );
        }

        $kembalian = $request->metode_pembayaran === 'Tunai' ? $jumlah_uang - $total_bayar : 0;

        try {
            DB::beginTransaction();

            // Generate nomor transaksi
            $no_transaksi = 'TRX-' . date('Ymd') . '-' . sprintf('%04d', Transaksi::count() + 1);

            // Hitung total item
            $total_item = array_sum(array_column($cart, 'qty'));

            // Buat transaksi
            $transaksi = Transaksi::create([
                'no_transaksi' => $no_transaksi,
                'kasir_id' => auth()->id(),
                'tanggal_transaksi' => Carbon::now(),
                'total_item' => $total_item,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'total_bayar' => $total_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_uang' => $jumlah_uang,
                'kembalian' => $kembalian,
                'status' => 'Selesai'
            ]);

            // Simpan items transaksi dan update stok
            foreach ($cart as $item) {
                // PERBAIKAN: Pastikan barcode selalu ada dengan fallback
                $barcode = $item['barcode'] ?? '';

                // Simpan item transaksi
                TransaksiItem::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'produk_id' => $item['produk_id'],
                    'barcode' => $barcode,
                    'nama_produk' => $item['nama_produk'],
                    'harga_jual' => $item['harga_jual'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal']
                ]);

                // Update stok produk
                $produk = Produk::find($item['produk_id']);
                if ($produk) {
                    $stok_sebelum = $produk->stok_tersedia;
                    $stok_sesudah = $stok_sebelum - $item['qty'];

                    // Update stok produk
                    $produk->update([
                        'stok_tersedia' => $stok_sesudah
                    ]);

                    // Catat history stok
                    $stokData = [
                        'produk_id' => $item['produk_id'],
                        'jenis_transaksi' => 'Keluar',
                        'jumlah' => $item['qty'],
                        'stok_sebelum' => $stok_sebelum,
                        'stok_sesudah' => $stok_sesudah,
                        'tanggal_transaksi' => Carbon::now(),
                        'user_id' => auth()->id(),
                        'keterangan' => 'Penjualan: ' . $no_transaksi,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];

                    // Cek apakah kolom transaksi_id ada di tabel stok
                    if (Schema::hasColumn('stok', 'transaksi_id')) {
                        $stokData['transaksi_id'] = $transaksi->transaksi_id;
                    }

                    Stok::create($stokData);
                }
            }

            // Update kas harian jika ada
            $kasHarian = KasHarian::where('tanggal', Carbon::today())->first();
            if ($kasHarian && $kasHarian->status === 'Open') {
                if ($request->metode_pembayaran === 'Tunai') {
                    $kasHarian->increment('penerimaan_tunai', $total_bayar);
                } else {
                    $kasHarian->increment('penerimaan_non_tunai', $total_bayar);
                }
                $kasHarian->update(['saldo_akhir' => $kasHarian->saldo_awal + $kasHarian->penerimaan_tunai - $kasHarian->pengeluaran]);
            }

            DB::commit();

            // Reset session
            session()->forget(['cart', 'subtotal', 'diskon', 'total_bayar', 'search_results', 'search_error', 'search_query']);

            return redirect()->route('kasir.cetak-struk', $transaksi->transaksi_id)
                ->with('success', 'Transaksi berhasil diproses');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Proses Transaksi Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            return redirect()->route('kasir.index')->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * CETAK STRUK - Preview struk untuk print
     */
    public function cetakStruk($id)
    {
        $transaksi = Transaksi::with(['items', 'kasir'])
            ->findOrFail($id);

        return view('kasir.struk', compact('transaksi'));
    }

    /**
     * GET PRODUCT DETAIL - Untuk modal detail produk
     */
    public function getProductDetail($id)
    {
        try {
            $produk = Produk::with('kategori')->findOrFail($id);

            $gambar_url = null;
            $gambar_exists = false;

            if ($produk->gambar_produk) {
                $gambar_path = 'storage/' . $produk->gambar_produk;
                $gambar_exists = file_exists(public_path($gambar_path));
                $gambar_url = $gambar_exists ? asset($gambar_path) : null;
            }

            return response()->json([
                'success' => true,
                'produk' => [
                    'produk_id' => $produk->produk_id,
                    'barcode' => $produk->barcode,
                    'nama_produk' => $produk->nama_produk,
                    'harga_jual' => $produk->harga_jual,
                    'stok_tersedia' => $produk->stok_tersedia,
                    'stok_minimum' => $produk->stok_minimum,
                    'kategori' => $produk->kategori->nama_kategori ?? 'Tidak ada kategori',
                    'gambar_produk' => $produk->gambar_produk,
                    'gambar_url' => $gambar_url,
                    'gambar_exists' => $gambar_exists,
                    'status' => $produk->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
    }

    /**
     * CLEAR SEARCH RESULTS - Hapus hasil pencarian
     */
    public function clearSearchResults()
    {
        session()->forget(['search_results', 'search_error', 'search_query']);
        return redirect()->route('kasir.index')->with('success', 'Hasil pencarian berhasil dihapus');
    }

    /**
     * GET CART SUMMARY - API untuk mendapatkan summary cart
     */
    public function getCartSummary()
    {
        $cart = session()->get('cart', []);
        $subtotal = session('subtotal', 0);
        $diskon = session('diskon', 0);
        $total_bayar = session('total_bayar', 0);

        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'total_bayar' => $total_bayar,
            'cart_items' => $cart
        ]);
    }

    /**
     * QUICK ADD TO CART FROM PRODUCT LIST - Untuk tombol "Tambah" di daftar produk
     */
    public function quickAddToCartFromList(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'qty' => 'required|integer|min:1'
        ]);

        try {
            $produk = Produk::findOrFail($request->produk_id);

            if ($produk->stok_tersedia < $request->qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok "' . $produk->nama_produk . '" tidak mencukupi. Stok tersedia: ' . $produk->stok_tersedia
                ]);
            }

            $cart = session()->get('cart', []);
            $existingIndex = null;

            foreach ($cart as $index => $item) {
                if ($item['produk_id'] == $request->produk_id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                $cart[$existingIndex]['qty'] += $request->qty;
                $cart[$existingIndex]['subtotal'] = $cart[$existingIndex]['qty'] * $cart[$existingIndex]['harga_jual'];
            } else {
                $cart[] = [
                    'produk_id' => $produk->produk_id,
                    'barcode' => $produk->barcode ?? '',
                    'nama_produk' => $produk->nama_produk,
                    'harga_jual' => $produk->harga_jual,
                    'qty' => $request->qty,
                    'subtotal' => $request->qty * $produk->harga_jual
                ];
            }

            session(['cart' => $cart]);
            $this->calculateCartTotal();

            return response()->json([
                'success' => true,
                'message' => 'Produk "' . $produk->nama_produk . '" berhasil ditambahkan ke keranjang',
                'cart_count' => count($cart),
                'subtotal' => session('subtotal', 0)
            ]);
        } catch (\Exception $e) {
            Log::error('Quick Add to Cart From List Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CHECK PRODUCT IMAGE - Cek status gambar produk
     */
    public function checkProductImage($id)
    {
        try {
            $produk = Produk::findOrFail($id);

            $image_info = [
                'has_image' => !empty($produk->gambar_produk),
                'image_path' => $produk->gambar_produk,
                'storage_path' => 'storage/' . $produk->gambar_produk,
                'asset_url' => $produk->gambar_produk ? asset('storage/' . $produk->gambar_produk) : null,
                'file_exists' => false,
                'public_path' => $produk->gambar_produk ? public_path('storage/' . $produk->gambar_produk) : null
            ];

            if ($produk->gambar_produk) {
                $image_info['file_exists'] = file_exists(public_path('storage/' . $produk->gambar_produk));
                $image_info['storage_exists'] = Storage::disk('public')->exists($produk->gambar_produk);
            }

            return response()->json([
                'success' => true,
                'produk' => [
                    'produk_id' => $produk->produk_id,
                    'nama_produk' => $produk->nama_produk,
                    'barcode' => $produk->barcode
                ],
                'image_info' => $image_info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
    }

    /**
     * GET PRODUCTS WITH IMAGES - Untuk debug gambar
     */
    public function getProductsWithImages()
    {
        $produk = Produk::where('status', 'Tersedia')
            ->whereNotNull('gambar_produk')
            ->limit(10)
            ->get(['produk_id', 'nama_produk', 'barcode', 'gambar_produk']);

        $products_with_images = [];

        foreach ($produk as $item) {
            $image_exists = false;
            $image_url = null;

            if ($item->gambar_produk) {
                $image_path = public_path('storage/' . $item->gambar_produk);
                $image_exists = file_exists($image_path);
                $image_url = $image_exists ? asset('storage/' . $item->gambar_produk) : null;
            }

            $products_with_images[] = [
                'produk_id' => $item->produk_id,
                'nama_produk' => $item->nama_produk,
                'barcode' => $item->barcode,
                'gambar_produk' => $item->gambar_produk,
                'image_exists' => $image_exists,
                'image_url' => $image_url
            ];
        }

        return response()->json([
            'success' => true,
            'products_count' => count($products_with_images),
            'products' => $products_with_images
        ]);
    }

    /**
     * VALIDATE STOCK - Validasi stok sebelum transaksi
     */
    public function validateStock(Request $request)
    {
        $cart = session()->get('cart', []);
        $errors = [];

        foreach ($cart as $index => $item) {
            $produk = Produk::find($item['produk_id']);
            if ($produk && $produk->stok_tersedia < $item['qty']) {
                $errors[] = "Stok {$produk->nama_produk} tidak mencukupi. Tersedia: {$produk->stok_tersedia}, Diminta: {$item['qty']}";
            }
        }

        return response()->json([
            'success' => empty($errors),
            'errors' => $errors
        ]);
    }

    /**
     * GET SUGGESTED PRODUCTS - Produk yang sering dibeli
     */
    public function getSuggestedProducts()
    {
        $suggestedProducts = Produk::where('status', 'Tersedia')
            ->where('stok_tersedia', '>', 0)
            ->inRandomOrder()
            ->limit(5)
            ->get(['produk_id', 'nama_produk', 'harga_jual', 'stok_tersedia']);

        return response()->json([
            'success' => true,
            'suggested_products' => $suggestedProducts
        ]);
    }

    /**
     * BULK ADD TO CART - Tambah banyak produk sekaligus
     */
    public function bulkAddToCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.produk_id' => 'required|exists:produk,produk_id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        try {
            $cart = session()->get('cart', []);
            $addedCount = 0;

            foreach ($request->items as $itemData) {
                $produk = Produk::find($itemData['produk_id']);

                if (!$produk || $produk->stok_tersedia < $itemData['qty']) {
                    continue;
                }

                $existingIndex = null;
                foreach ($cart as $index => $item) {
                    if ($item['produk_id'] == $itemData['produk_id']) {
                        $existingIndex = $index;
                        break;
                    }
                }

                if ($existingIndex !== null) {
                    $cart[$existingIndex]['qty'] += $itemData['qty'];
                    $cart[$existingIndex]['subtotal'] = $cart[$existingIndex]['qty'] * $cart[$existingIndex]['harga_jual'];
                } else {
                    $cart[] = [
                        'produk_id' => $produk->produk_id,
                        'barcode' => $produk->barcode ?? '',
                        'nama_produk' => $produk->nama_produk,
                        'harga_jual' => $produk->harga_jual,
                        'qty' => $itemData['qty'],
                        'subtotal' => $itemData['qty'] * $produk->harga_jual
                    ];
                }
                $addedCount++;
            }

            session(['cart' => $cart]);
            $this->calculateCartTotal();

            return response()->json([
                'success' => true,
                'message' => "{$addedCount} produk berhasil ditambahkan ke keranjang",
                'cart_count' => count($cart)
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk Add to Cart Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}