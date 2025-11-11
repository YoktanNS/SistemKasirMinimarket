<?php
namespace App\Http\Controllers; // <-- INI YANG DIPERBAIKI

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class PosController extends Controller 
{
    public function index() 
    {
        // Menggunakan kolom 'stok_tersedia' dari tabel 'produk' Anda
        $fastGridProducts = Product::with('kategori')
                            ->orderBy('stok_tersedia', 'desc')
                            ->take(4)
                            ->get();
                            
        return view('tps', compact('fastGridProducts'));
    }
}