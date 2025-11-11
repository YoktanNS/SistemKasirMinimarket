<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory;
    protected $table = 'kategori_produk';
    protected $primaryKey = 'kategori_id';
    public $timestamps = false; // Sepertinya tidak ada updated_at
}