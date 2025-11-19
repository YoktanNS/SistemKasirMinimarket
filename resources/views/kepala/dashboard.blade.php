@extends('layouts.kepala')

@section('title', 'Dashboard Kepala Minimarket - SmartMart Campus')

@section('content')
<div class="space-y-6">
    <!-- Header Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Penjualan Hari Ini -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['penjualan_hari_ini'], 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['transaksi_hari_ini'] }} transaksi</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-chart-line text-green-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Penjualan Minggu Ini -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Minggu Ini</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['penjualan_minggu_ini'], 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['transaksi_minggu_ini'] }} transaksi</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-calendar-week text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Penjualan Bulan Ini -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['penjualan_bulan_ini'], 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['transaksi_bulan_ini'] }} transaksi</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-calendar-alt text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Status Stok -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Status Stok</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_produk'] }} produk</p>
                    <div class="flex space-x-2 text-xs">
                        <span class="text-red-500">{{ $stats['stok_habis'] }} habis</span>
                        <span class="text-orange-500">{{ $stats['stok_menipis'] }} menipis</span>
                    </div>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-boxes text-orange-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Performance Metrics -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Performance Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Rata-rata Transaksi -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">Rata-rata Transaksi</h3>
                        <i class="fas fa-receipt text-blue-500"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($performance['rata_transaksi_hari'], 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">Per transaksi hari ini</p>
                </div>

                <!-- Profit Margin -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">Profit Margin</h3>
                        <i class="fas fa-percentage text-green-500"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($performance['profit_margin'], 1) }}%</p>
                    <p class="text-sm text-gray-500 mt-1">Estimasi hari ini</p>
                </div>
            </div>

            <!-- Kasir Aktif Hari Ini -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Kasir Aktif Hari Ini</h3>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $kasirAktif->count() }} kasir
                    </span>
                </div>
                <div class="space-y-4">
                    @forelse($kasirAktif as $kasir)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-800 w-10 h-10 rounded-full flex items-center justify-center font-semibold">
                                {{ substr($kasir->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-800">{{ $kasir->nama_lengkap }}</p>
                                <p class="text-sm text-gray-600">{{ $kasir->transaksi_count }} transaksi</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600">Rp {{ number_format($kasir->transaksi_sum_total_bayar, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600">Total penjualan</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users text-4xl mb-3"></i>
                        <p>Belum ada kasir aktif hari ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('kepala.laporan.kas') }}" 
                       class="flex items-center p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-file-invoice-dollar mr-3"></i>
                        <span>Laporan Kas</span>
                    </a>
                    <a href="{{ route('kepala.laporan.penjualan') }}" 
                       class="flex items-center p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Laporan Penjualan</span>
                    </a>
                    <a href="#" 
                       class="flex items-center p-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition">
                        <i class="fas fa-boxes mr-3"></i>
                        <span>Manajemen Stok</span>
                    </a>
                    <a href="#" 
                       class="flex items-center p-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition">
                        <i class="fas fa-users mr-3"></i>
                        <span>Manajemen Kasir</span>
                    </a>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Produk Terlaris</h3>
                <div class="space-y-3">
                    @forelse($produkTerlaris as $produk)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 text-sm">{{ $produk->nama_produk }}</p>
                            <p class="text-xs text-gray-600">{{ $produk->total_terjual }} terjual</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-600">Rp {{ number_format($produk->total_pendapatan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm text-center">Belum ada data penjualan</p>
                    @endforelse
                </div>
            </div>

            <!-- Status Kas Hari Ini -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Kas Hari Ini</h3>
                @if($kasHarian)
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-semibold {{ $kasHarian->status == 'Open' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $kasHarian->status }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Saldo Akhir:</span>
                        <span class="font-semibold text-green-600">Rp {{ number_format($kasHarian->saldo_akhir, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Penerimaan:</span>
                        <span class="font-semibold">Rp {{ number_format($kasHarian->penerimaan_tunai + $kasHarian->penerimaan_non_tunai, 0, ',', '.') }}</span>
                    </div>
                </div>
                @else
                <p class="text-gray-500 text-sm text-center">Kas belum dibuka hari ini</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru & Pengeluaran Besar -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaksi Terbaru -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
                <a href="{{ route('kepala.laporan.penjualan') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    Lihat Semua →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($transaksiTerbaru as $transaksi)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $transaksi->no_transaksi }}</p>
                        <p class="text-sm text-gray-600">{{ $transaksi->kasir->nama_lengkap ?? 'Unknown' }} • {{ $transaksi->tanggal_transaksi->format('H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-600">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">{{ $transaksi->metode_pembayaran }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada transaksi hari ini</p>
                @endforelse
            </div>
        </div>

        <!-- Pengeluaran Besar -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Pengeluaran Besar</h3>
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                    > Rp 100k
                </span>
            </div>
            <div class="space-y-4">
                @forelse($pengeluaranBesar as $pengeluaran)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $pengeluaran->keterangan }}</p>
                        <p class="text-sm text-gray-600">{{ $pengeluaran->kategori }} • {{ $pengeluaran->created_at->format('H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Tidak ada pengeluaran besar hari ini</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Auto Refresh Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh setiap 2 menit
    setInterval(refreshDashboard, 120000);
    
    function refreshDashboard() {
        fetch('{{ route("kepala.dashboard.data") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Dashboard updated:', data.last_updated);
                }
            })
            .catch(error => console.log('Auto-refresh failed:', error));
    }
});
</script>
@endsection