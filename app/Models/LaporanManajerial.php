<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanManajerial extends Model
{
    use HasFactory; // HAPUS SoftDeletes

    protected $table = 'laporan_manajerial';
    protected $primaryKey = 'laporan_id';
    public $timestamps = true;

    protected $fillable = [
        'jenis_laporan',
        'periode_awal', 
        'periode_akhir',
        'data_laporan',
        'ringkasan',
        'rekomendasi',
        'created_by'
    ];

    protected $casts = [
        'data_laporan' => 'array',
        'periode_awal' => 'date',
        'periode_akhir' => 'date'
    ];

    // Konstanta untuk jenis laporan
    const JENIS_HARIAN = 'Penjualan Harian';
    const JENIS_BULANAN = 'Penjualan Bulanan';
    const JENIS_PRODUK_TERLARIS = 'Produk Terlaris';
    const JENIS_PROFIT_MARGIN = 'Profit Margin';
    const JENIS_PERFORMA_SUPPLIER = 'Performa Supplier';
    const JENIS_CUSTOM = 'Custom';

    public static function getJenisLaporanOptions()
    {
        return [
            self::JENIS_HARIAN => 'Laporan Harian',
            self::JENIS_BULANAN => 'Laporan Bulanan', 
            self::JENIS_PRODUK_TERLARIS => 'Produk Terlaris',
            self::JENIS_PROFIT_MARGIN => 'Profit Margin',
            self::JENIS_PERFORMA_SUPPLIER => 'Performa Supplier',
            self::JENIS_CUSTOM => 'Laporan Kustom'
        ];
    }

    // Accessors
    public function getPeriodeLengkapAttribute()
    {
        if ($this->periode_awal && $this->periode_akhir) {
            if ($this->periode_awal->eq($this->periode_akhir)) {
                return $this->periode_awal->translatedFormat('d F Y');
            }
            return $this->periode_awal->translatedFormat('d F Y') . ' - ' . $this->periode_akhir->translatedFormat('d F Y');
        }
        return 'Periode tidak ditentukan';
    }

    public function getJudulLaporanAttribute()
    {
        return $this->jenis_laporan . ' - ' . $this->periode_lengkap;
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}