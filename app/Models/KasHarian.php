<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasHarian extends Model
{
    use HasFactory;

    protected $table = 'kas_harian';
    
    protected $fillable = [
        'tanggal',
        'saldo_awal',
        'penerimaan_tunai',
        'pengeluaran',
        'saldo_akhir',
        'status', // Open, Closed
        'user_id',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'saldo_awal' => 'decimal:2',
        'penerimaan_tunai' => 'decimal:2',
        'pengeluaran' => 'decimal:2',
        'saldo_akhir' => 'decimal:2'
    ];

    // Relasi sederhana
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'kas_harian_id');
    }

    // Method helper sederhana
    public static function kasHariIni()
    {
        return static::where('tanggal', today())->first();
    }

    public function isOpen()
    {
        return $this->status === 'Open';
    }
}