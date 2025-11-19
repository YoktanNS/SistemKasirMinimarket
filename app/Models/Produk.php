<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'produk_id';
    public $timestamps = true;

    protected $fillable = [
        'barcode',
        'nama_produk',
        'kategori_id',
        'supplier_id',
        'harga_beli',
        'harga_jual',
        'stok_tersedia',
        'stok_minimum',
        'satuan',
        'gambar_produk',
        'deskripsi',
        'status',
    ];

    // === RELASI ===
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stok()
    {
        return $this->hasMany(Stok::class, 'produk_id');
    }

}