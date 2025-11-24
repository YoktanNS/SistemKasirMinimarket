<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = [
        'password',
        'nama_lengkap',
        'email',
        'no_telepon',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'kasir_id');
    }

    public function stok()
    {
        return $this->hasMany(Stok::class, 'user_id');
    }

    public function isKepala()
    {
        return $this->role === 'Kepala';
    }

    public function isAdmin()
    {
    return $this->role === 'Admin'; 
    }

    public function isKasir()
    {
        return $this->role === 'Kasir';
    }
}