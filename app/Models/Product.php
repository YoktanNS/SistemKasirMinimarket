<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Memberitahu Laravel untuk menggunakan tabel 'produk'
    protected $table = 'produk';

    // Memberitahu Laravel bahwa primary key-nya adalah 'produk_id'
    protected $primaryKey = 'produk_id';

    // Kita tidak menggunakan 'created_at'/'updated_at' bawaan Laravel
    // karena nama kolom Anda 'created_at' dan 'updated_at' (sudah cocok)
    // tapi jika error, nonaktifkan dengan: public $timestamps = false;
    
    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id', 'kategori_id');
    }
}