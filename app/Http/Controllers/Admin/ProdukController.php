<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class ProdukController extends Controller
{
    // ===========================
    // TAMPIL DATA PRODUK + SEARCH
    // ===========================
    public function index(Request $request)
    {
        $produk = Produk::with(['kategori', 'supplier'])
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('nama_supplier', 'like', "%{$search}%");
                    })
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->orderBy('produk_id', 'desc')
            ->paginate(10);

        return view('admin.produk.index', compact('produk'));
    }

    // ===========================
    // FORM TAMBAH PRODUK - FIXED
    // ===========================
    public function create()
    {
        $suppliers = Supplier::where('status', 'Aktif')->get();
        $kategoris = KategoriProduk::all();

        return view('admin.produk.create', compact('suppliers', 'kategoris'));
    }

    // ===========================
    // SIMPAN PRODUK BARU
    // ===========================
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategori_produk,kategori_id',
            'supplier_id' => 'required|exists:supplier,supplier_id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            // Generate barcode otomatis
            $barcode = 'PRD-' . strtoupper(uniqid()) . '-' . time();

            // Handle upload gambar
            $gambarPath = null;
            if ($request->hasFile('gambar_produk')) {
                $gambarPath = $request->file('gambar_produk')->store('produk', 'public');
            }

            // Create produk
            Produk::create([
                'barcode' => $barcode,
                'nama_produk' => $validated['nama_produk'],
                'kategori_id' => $validated['kategori_id'],
                'supplier_id' => $validated['supplier_id'],
                'harga_beli' => $validated['harga_beli'],
                'harga_jual' => $validated['harga_jual'],
                'stok_tersedia' => 0,
                'stok_minimum' => $validated['stok_minimum'],
                'satuan' => $validated['satuan'],
                'gambar_produk' => $gambarPath,
                'deskripsi' => $validated['deskripsi'],
                'status' => 'Tersedia'
            ]);

            return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ===========================
    // FORM EDIT PRODUK - FIXED
    // ===========================
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $suppliers = Supplier::where('status', 'Aktif')->get();
        $kategoris = KategoriProduk::all();

        return view('admin.produk.edit', compact('produk', 'suppliers', 'kategoris'));
    }

    // ===========================
    // UPDATE PRODUK - FIXED
    // ===========================
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            // HAPUS validasi barcode - karena sudah auto generate dan tidak perlu diubah
            'nama_produk' => 'required|string|max:100',
            'kategori_id' => 'nullable|exists:kategori_produk,kategori_id',
            'supplier_id' => 'nullable|exists:supplier,supplier_id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_tersedia' => 'nullable|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'status' => 'in:Tersedia,Habis,Nonaktif',
            'deskripsi' => 'nullable|string',
        ]);

        // Handle upload gambar jika ada
        if ($request->hasFile('gambar_produk')) {
            $validated['gambar_produk'] = $request->file('gambar_produk')->store('produk', 'public');
            
            // Hapus gambar lama jika ada
            if ($produk->gambar_produk) {
                Storage::disk('public')->delete($produk->gambar_produk);
            }
        }

        $produk->update($validated);
        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // ===========================
    // HAPUS PRODUK
    // ===========================
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        
        // Hapus gambar jika ada
        if ($produk->gambar_produk) {
            Storage::disk('public')->delete($produk->gambar_produk);
        }
        
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}