<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    use HasFactory;

    protected $table = 'transaksi_items';
    protected $primaryKey = 'transaksi_item_id';
    
    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'barcode',
        'nama_produk',
        'qty',
        'harga_jual',
        'subtotal'
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}