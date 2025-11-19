<?php

namespace App\Helpers;

use Carbon\Carbon;

class DashboardHelper
{
    /**
     * Get current shift based on time
     */
    public static function getCurrentShift()
    {
        $hour = now()->hour;
        
        if ($hour >= 6 && $hour < 14) {
            return 'Pagi';
        } elseif ($hour >= 14 && $hour < 22) {
            return 'Siang';
        } else {
            return 'Malam';
        }
    }

    /**
     * Calculate transactions per hour
     */
    public static function calculateTransactionsPerHour($totalTransactions)
    {
        $hour = now()->hour;
        $startHour = 8; // Store opens at 8 AM
        $operatingHours = max($hour - $startHour, 1);
        
        return number_format($totalTransactions / $operatingHours, 1);
    }

    /**
     * Calculate profit margin
     */
    public static function calculateProfitMargin($stats)
    {
        $revenue = $stats['total_penjualan'] ?? 0;
        $expenses = $stats['total_pengeluaran'] ?? 0;
        $profit = $revenue - $expenses;
        
        if ($revenue > 0) {
            return number_format(($profit / $revenue) * 100, 1);
        }
        
        return 0.0;
    }

    /**
     * Calculate average transaction value
     */
    public static function calculateAverageTransaction($stats)
    {
        $totalSales = $stats['total_penjualan'] ?? 0;
        $totalTransactions = $stats['total_transaksi'] ?? 0;
        
        if ($totalTransactions > 0) {
            return $totalSales / $totalTransactions;
        }
        
        return 0;
    }

    /**
     * Get current time in Indonesia format
     */
    public static function getCurrentTime()
    {
        return now()->format('H:i:s');
    }

    /**
     * Format currency for Indonesia
     */
    public static function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}