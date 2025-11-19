<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaksi extends Model
{
    use HasFactory;

    // Constants untuk konsistensi
    const METODE_TUNAI = 'Tunai';
    const METODE_DEBIT = 'Debit';
    const METODE_QRIS = 'QRIS';
    const METODE_TRANSFER = 'Transfer';
    
    const STATUS_SELESAI = 'Selesai';
    const STATUS_DIBATALKAN = 'Dibatalkan';

    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    public $timestamps = true;

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
        'kembalian',
        'status',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'jumlah_uang' => 'decimal:2',
        'kembalian' => 'decimal:2'
    ];

    /**
     * Get metode pembayaran options
     */
    public static function getMetodePembayaranOptions()
    {
        return [
            self::METODE_TUNAI,
            self::METODE_DEBIT,
            self::METODE_QRIS,
            self::METODE_TRANSFER
        ];
    }

    /**
     * Relasi ke user (kasir)
     */
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id', 'user_id');
    }

    /**
     * Relasi ke detail transaksi (items)
     */
    public function items()
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id', 'transaksi_id');
    }

    /**
     * Relasi ke transaksi items (alias untuk items)
     */
    public function transaksiItems()
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id', 'transaksi_id');
    }

    /**
     * Scope untuk transaksi hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_transaksi', today());
    }

    /**
     * Scope untuk transaksi oleh kasir tertentu
     */
    public function scopeByKasir($query, $kasirId)
    {
        return $query->where('kasir_id', $kasirId);
    }

    /**
     * Scope untuk transaksi selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    /**
     * Hitung total transaksi hari ini
     */
    public static function totalHariIni()
    {
        return self::hariIni()->selesai()->sum('total_bayar');
    }

    /**
     * Generate nomor transaksi baru
     */
    public static function generateNoTransaksi()
    {
        $date = now()->format('Ymd');
        $lastTransaction = self::where('no_transaksi', 'like', "TRX-$date-%")
            ->orderBy('no_transaksi', 'desc')
            ->first();

        $sequence = $lastTransaction ? 
            (int) substr($lastTransaction->no_transaksi, -4) + 1 : 1;

        return "TRX-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted tanggal transaksi
     */
    public function getTanggalFormatAttribute()
    {
        return $this->tanggal_transaksi->format('d/m/Y H:i');
    }

    /**
     * Get formatted total bayar
     */
    public function getTotalBayarFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    /**
     * Get formatted jumlah uang
     */
    public function getJumlahUangFormatAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_uang, 0, ',', '.');
    }

    /**
     * Get formatted kembalian
     */
    public function getKembalianFormatAttribute()
    {
        return 'Rp ' . number_format($this->kembalian, 0, ',', '.');
    }

    /**
     * Check if transaction can be cancelled (within 24 hours)
     */
    public function canBeCancelled()
    {
        return $this->status === self::STATUS_SELESAI && 
               $this->tanggal_transaksi->diffInHours(now()) <= 24;
    }
}