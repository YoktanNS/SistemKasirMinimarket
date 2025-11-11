<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\KategoriProduk; // Kita tambahkan kategori

class ProductSeeder extends Seeder {
    public function run(): void {
        // Buat Kategori dulu
        $katMakanan = KategoriProduk::create(['nama_kategori' => 'Makanan']);
        $katMinuman = KategoriProduk::create(['nama_kategori' => 'Minuman']);
        $katATK = KategoriProduk::create(['nama_kategori' => 'ATK']);

        Product::create([
            'barcode' => '089686011010',
            'nama_produk' => 'Indomie Goreng',
            'kategori_id' => $katMakanan->kategori_id,
            'harga_beli' => 3000,
            'harga_jual' => 3500,
            'stok_tersedia' => 150,
            'stok_minimum' => 10,
            'satuan' => 'pcs'
        ]);
        Product::create([
            'barcode' => '089686111217',
            'nama_produk' => 'Aqua 600ml',
            'kategori_id' => $katMinuman->kategori_id,
            'harga_beli' => 3000,
            'harga_jual' => 4000,
            'stok_tersedia' => 200,
            'stok_minimum' => 10,
            'satuan' => 'btl'
        ]);
        Product::create([
            'barcode' => '765432109876',
            'nama_produk' => 'Pulpen Standard',
            'kategori_id' => $katATK->kategori_id,
            'harga_beli' => 2000,
            'harga_jual' => 2500,
            'stok_tersedia' => 80,
            'stok_minimum' => 5,
            'satuan' => 'pcs'
        ]);
        Product::create([
            'barcode' => '9876543210221',
            'nama_produk' => 'Snack Chitato',
            'kategori_id' => $katMakanan->kategori_id,
            'harga_beli' => 8500,
            'harga_jual' => 10000,
            'stok_tersedia' => 60,
            'stok_minimum' => 5,
            'satuan' => 'pcs'
        ]);
    }
}