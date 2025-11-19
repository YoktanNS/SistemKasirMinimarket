<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    // ===========================
    // TAMPIL DAFTAR KATEGORI + SEARCH
    // ===========================
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $kategori = KategoriProduk::when($search, function ($query) use ($search) {
                $query->where('nama_kategori', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kategori.index', compact('kategori'));
    }

    // ===========================
    // FORM TAMBAH KATEGORI
    // ===========================
    public function create()
    {
        return view('admin.kategori.create');
    }

    // ===========================
    // SIMPAN KATEGORI BARU
    // ===========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori_produk,nama_kategori',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        try {
            KategoriProduk::create($validated);
            
            return redirect()->route('admin.kategori.index')
                ->with('success', 'Kategori berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ===========================
    // FORM EDIT KATEGORI
    // ===========================
    public function edit($id)
    {
        $kategori = KategoriProduk::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    // ===========================
    // UPDATE KATEGORI
    // ===========================
    public function update(Request $request, $id)
    {
        $kategori = KategoriProduk::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => "required|string|max:50|unique:kategori_produk,nama_kategori,{$id},kategori_id",
            'deskripsi' => 'nullable|string|max:255',
        ]);

        try {
            $kategori->update($validated);
            
            return redirect()->route('admin.kategori.index')
                ->with('success', 'Kategori berhasil diperbarui!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ===========================
    // HAPUS KATEGORI
    // ===========================
    public function destroy($id)
    {
        $kategori = KategoriProduk::findOrFail($id);

        try {
            // Cek apakah kategori digunakan oleh produk
            $produkCount = $kategori->produk()->count();
            
            if ($produkCount > 0) {
                return back()->with('error', 
                    "Tidak dapat menghapus kategori! Kategori ini digunakan oleh {$produkCount} produk.");
            }

            $kategori->delete();
            
            return back()->with('success', 'Kategori berhasil dihapus!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    // ===========================
    // API UNTUK DROPDOWN (OPSIONAL)
    // ===========================
    public function getKategoriJson()
    {
        $kategori = KategoriProduk::select('kategori_id', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();
            
        return response()->json($kategori);
    }
}