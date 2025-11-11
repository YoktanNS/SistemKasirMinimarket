<?php
namespace App\Http\Controllers\Api; // <-- INI YANG DIPERBAIKI

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller 
{
    public function show($barcode) 
    {
        // Mencari berdasarkan 'barcode'
        $product = Product::where('barcode', $barcode)->first();
        
        if ($product) {
            return response()->json($product);
        }
        return response()->json(['message' => 'Produk tidak ditemukan'], 404);
    }
}