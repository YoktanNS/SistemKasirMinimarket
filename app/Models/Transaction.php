<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    
    // Kolom yang boleh diisi massal
    protected $fillable = [
        'no_transaksi', 
        'kasir_id', 
        'tanggal_transaksi',
        'total_item', 
        'subtotal', 
        'diskon', 
        'total_bayar',
        'metode_pembayaran', 
        'jumlah_uang', 
        'kembalian'
    ];
}