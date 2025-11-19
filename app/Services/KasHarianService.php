<?php

namespace App\Services;

use App\Models\KasHarian;
use App\Models\Transaksi;

class KasHarianService
{
    /**
     * Update kas harian dari transaksi baru
     */
    public static function updateFromTransaksi(Transaksi $transaksi)
    {
        $kasHarian = KasHarian::where('tanggal', today())->first();
        
        if (!$kasHarian) {
            // Create new kas harian record jika belum ada
            $kasHarian = KasHarian::create([
                'tanggal' => today(),
                'saldo_awal' => 0,
                'penerimaan' => 0,
                'pengeluaran' => 0,
                'saldo_akhir' => 0,
                'status' => 'Aktif'
            ]);
        }

        // Update penerimaan
        $kasHarian->increment('penerimaan', $transaksi->total_bayar);
        
        // Recalculate saldo akhir
        self::recalculateSaldoAkhir($kasHarian);
        
        return $kasHarian;
    }

    /**
     * Update kas harian dari transaksi batal
     */
    public static function updateFromTransaksiBatal(Transaksi $transaksi)
    {
        $kasHarian = KasHarian::where('tanggal', $transaksi->tanggal_transaksi->toDateString())->first();
        
        if ($kasHarian) {
            // Kurangi penerimaan
            $kasHarian->decrement('penerimaan', $transaksi->total_bayar);
            
            // Recalculate saldo akhir
            self::recalculateSaldoAkhir($kasHarian);
        }
        
        return $kasHarian;
    }

    /**
     * Recalculate saldo akhir
     */
    public static function recalculateSaldoAkhir(KasHarian $kasHarian)
    {
        $saldoAkhir = $kasHarian->saldo_awal + $kasHarian->penerimaan - $kasHarian->pengeluaran;
        $kasHarian->update(['saldo_akhir' => $saldoAkhir]);
        
        return $saldoAkhir;
    }

    /**
     * Set saldo awal untuk hari ini
     */
    public static function setSaldoAwal($saldoAwal)
    {
        $kasHarian = KasHarian::where('tanggal', today())->first();
        
        if (!$kasHarian) {
            $kasHarian = KasHarian::create([
                'tanggal' => today(),
                'saldo_awal' => $saldoAwal,
                'penerimaan' => 0,
                'pengeluaran' => 0,
                'saldo_akhir' => $saldoAwal,
                'status' => 'Aktif'
            ]);
        } else {
            $kasHarian->update([
                'saldo_awal' => $saldoAwal,
                'saldo_akhir' => $saldoAwal + $kasHarian->penerimaan - $kasHarian->pengeluaran
            ]);
        }
        
        return $kasHarian;
    }
}