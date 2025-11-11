<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $table = 'detail_transaksi';
    protected $primaryKey = 'detail_id';
    public $timestamps = false; // Database Anda sepertinya tidak punya ini

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'transaksi_id', 
        'produk_id', 
        'nama_produk',
        'harga_satuan', 
        'jumlah', 
        'subtotal'
    ];

    // Relasi ke Produk
    public function product()
    {
        // 'produk_id' di tabel ini, 'produk_id' di tabel produk
        return $this->belongsTo(Product::class, 'produk_id', 'produk_id');
    }
    
    // --- FUNGSI INI YANG BARU & PENTING ---
    // Relasi ke Transaksi (Header)
    public function transaction()
    {
        // 'transaksi_id' di tabel ini, 'transaksi_id' di tabel transaksi
        return $this->belongsTo(Transaction::class, 'transaksi_id', 'transaksi_id');
    }
}