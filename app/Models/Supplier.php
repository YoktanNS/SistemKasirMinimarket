<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $primaryKey = 'supplier_id';
    public $timestamps = true;

    protected $fillable = [
        // ❌ HAPUS user_id & kolom complex lainnya
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'no_telepon',
        'email',
        'kontak_person',
        'status', // Hanya ini yang tersisa
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'supplier_id');
    }
}