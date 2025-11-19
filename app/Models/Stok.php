<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';
    protected $primaryKey = 'stok_id';
    public $timestamps = true;

    protected $fillable = [
        'produk_id',
        'supplier_id',        
        'jenis_transaksi',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'tanggal_transaksi',
        'user_id',
        'keterangan',
        'transaksi_id' // TAMBAHAN: untuk relasi ke transaksi  
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer'
    ];

    // Relasi ke produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    // Relasi ke transaksi (jika ada)
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    // Scope untuk filter jenis transaksi
    public function scopeMasuk($query)
    {
        return $query->where('jenis_transaksi', 'Masuk');
    }

    public function scopeKeluar($query)
    {
        return $query->where('jenis_transaksi', 'Keluar');
    }

    /**
     * Update stok dengan transaction safety (DIPERBAIKI)
     */
    public static function updateStok($produkId, $jenis, $jumlah, $keterangan = null, $transaksiId = null)
    {
        return DB::transaction(function () use ($produkId, $jenis, $jumlah, $keterangan, $transaksiId) {
            // Lock produk untuk prevent race condition
            $produk = Produk::where('produk_id', $produkId)
                ->lockForUpdate()
                ->first();

            if (!$produk) {
                throw new \Exception("Produk tidak ditemukan");
            }

            $stokSebelum = $produk->stok_tersedia;
            
            // Validasi stok untuk pengurangan
            if ($jenis === 'Keluar' && $stokSebelum < $jumlah) {
                throw new \Exception("Stok tidak mencukupi. Stok tersedia: {$stokSebelum}");
            }

            // Hitung stok sesudah
            $stokSesudah = $jenis === 'Masuk' 
                ? $stokSebelum + $jumlah 
                : $stokSebelum - $jumlah;

            // Update produk stok
            $produk->update(['stok_tersedia' => $stokSesudah]);

            // Update status otomatis
            $newStatus = $stokSesudah <= 0 ? 'Habis' : 
                        ($stokSesudah <= $produk->stok_minimum ? 'Menipis' : 'Tersedia');
            $produk->update(['status' => $newStatus]);

            // Create stok record
            return self::create([
                'produk_id' => $produkId,
                'jenis_transaksi' => $jenis,
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'tanggal_transaksi' => now(),
                'user_id' => auth()->id(),
                'keterangan' => $keterangan,
                'transaksi_id' => $transaksiId
            ]);
        });
    }
}