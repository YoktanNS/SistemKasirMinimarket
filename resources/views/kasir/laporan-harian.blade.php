<!-- resources/views/kasir/laporan-harian.blade.php -->
@extends('layouts.kasir')

@section('title', 'Laporan Harian')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar mr-3 text-blue-500"></i>
                Laporan Harian
            </h1>
            <p class="text-gray-600 mt-1">
                <i class="fas fa-calendar mr-2"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold flex items-center">
                <i class="fas fa-print mr-2"></i>
                Print Halaman
            </button>
            
            <a href="{{ route('kasir.dashboard') }}" 
               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Transaksi -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalTransaksi }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-receipt text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Rata-rata Transaksi -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata/Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-calculator text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Item Terjual -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Item Terjual</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalItemTerjual }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-boxes text-orange-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Metode Pembayaran & Transaksi -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Metode Pembayaran -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-green-500"></i>
                    Metode Pembayaran
                </h2>
                <div class="space-y-4">
                    @foreach($metodePembayaran as $metode)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                @if($metode->metode_pembayaran == 'Tunai')
                                    <i class="fas fa-money-bill-wave text-green-500"></i>
                                @elseif($metode->metode_pembayaran == 'Debit')
                                    <i class="fas fa-credit-card text-blue-500"></i>
                                @elseif($metode->metode_pembayaran == 'QRIS')
                                    <i class="fas fa-qrcode text-purple-500"></i>
                                @else
                                    <i class="fas fa-exchange-alt text-gray-500"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $metode->metode_pembayaran }}</p>
                                <p class="text-sm text-gray-600">{{ $metode->total }} transaksi</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600 text-lg">Rp {{ number_format($metode->jumlah, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $totalPenjualan > 0 ? number_format(($metode->jumlah / $totalPenjualan) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Transaksi Hari Ini -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-list-alt mr-3 text-blue-500"></i>
                    Transaksi Hari Ini ({{ $transaksiHariIni->count() }})
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No. Transaksi</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Waktu</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Metode</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Items</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($transaksiHariIni as $index => $trx)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $trx->no_transaksi }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $trx->tanggal_transaksi->format('H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $trx->metode_pembayaran == 'Tunai' ? 'bg-green-100 text-green-800' : 
                                           ($trx->metode_pembayaran == 'Debit' ? 'bg-blue-100 text-blue-800' : 
                                           ($trx->metode_pembayaran == 'QRIS' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $trx->metode_pembayaran }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $trx->total_item }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-green-600 text-right">
                                    Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-receipt text-4xl mb-3"></i>
                                    <p>Belum ada transaksi hari ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 border-t border-gray-200">
                                <td colspan="5" class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">TOTAL:</td>
                                <td class="px-4 py-3 text-sm font-semibold text-green-700 text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column - Ringkasan Kas & Statistik -->
        <div class="space-y-6">
            <!-- Ringkasan Kas -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-cash-register mr-3 text-yellow-500"></i>
                    Ringkasan Kas
                </h2>
                <div class="space-y-4">
                    @if($kasHarian)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Saldo Awal</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($kasHarian->saldo_awal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-green-600">Penerimaan Tunai</span>
                        <span class="font-semibold text-green-700">Rp {{ number_format($kasHarian->penerimaan_tunai, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium text-purple-600">Penerimaan Non-Tunai</span>
                        <span class="font-semibold text-purple-700">Rp {{ number_format($kasHarian->penerimaan_non_tunai, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-blue-600">Total Penerimaan</span>
                        <span class="font-semibold text-blue-700">Rp {{ number_format($kasHarian->total_penerimaan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg border-t-2 border-indigo-300">
                        <span class="text-sm font-bold text-indigo-600">Saldo Akhir</span>
                        <span class="font-bold text-indigo-700 text-lg">Rp {{ number_format($kasHarian->saldo_akhir, 0, ',', '.') }}</span>
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-cash-register text-2xl mb-2"></i>
                        <p class="text-sm">Kas belum dibuka</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistik Waktu -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-line mr-3 text-indigo-500"></i>
                    Statistik Waktu
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Transaksi Pertama</span>
                        <span class="font-semibold text-gray-800">
                            @if($transaksiHariIni->count() > 0)
                                {{ $transaksiHariIni->last()->tanggal_transaksi->format('H:i') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Transaksi Terakhir</span>
                        <span class="font-semibold text-gray-800">
                            @if($transaksiHariIni->count() > 0)
                                {{ $transaksiHariIni->first()->tanggal_transaksi->format('H:i') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Jam Operasional</span>
                        <span class="font-semibold text-gray-800">
                            @if($transaksiHariIni->count() > 1)
                                @php
                                    $first = $transaksiHariIni->last()->tanggal_transaksi;
                                    $last = $transaksiHariIni->first()->tanggal_transaksi;
                                    $diff = $last->diffInHours($first);
                                @endphp
                                {{ $diff }} jam
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Rata-rata Transaksi/Jam</span>
                        <span class="font-semibold text-gray-800">
                            @if($transaksiHariIni->count() > 1 && $diff > 0)
                                {{ number_format($transaksiHariIni->count() / $diff, 1) }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-bolt mr-3 text-orange-500"></i>
                    Quick Actions
                </h2>
                <div class="space-y-3">
                    <a href="{{ route('kasir.index') }}" 
                       class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-shopping-cart text-blue-500 mr-3"></i>
                        <span class="font-medium text-blue-700">Transaksi Baru</span>
                    </a>
                    <a href="{{ route('kasir.riwayat') }}" 
                       class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
                        <i class="fas fa-history text-green-500 mr-3"></i>
                        <span class="font-medium text-green-700">Riwayat Transaksi</span>
                    </a>
                    <a href="{{ route('kasir.kas-harian.index') }}" 
                       class="flex items-center p-3 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition">
                        <i class="fas fa-cash-register text-purple-500 mr-3"></i>
                        <span class="font-medium text-purple-700">Kelola Kas</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
        font-size: 12pt;
    }
    
    .bg-gray-50, .bg-white {
        background: white !important;
        border: 1px solid #ddd !important;
    }
    
    .text-gray-600, .text-gray-800 {
        color: black !important;
    }
}
</style>

<script>
// Auto refresh data setiap 2 menit
setInterval(() => {
    window.location.reload();
}, 120000);
</script>
@endsection