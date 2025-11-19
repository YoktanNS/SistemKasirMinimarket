<!-- resources/views/kasir/dashboard.blade.php -->
@extends('layouts.kasir')

@section('title', 'Dashboard Kas Harian - SmartMart Campus')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    <!-- Main Content - Kas Harian & Stats -->
    <div class="flex-1 space-y-6">
        <!-- Header Kas -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-cash-register mr-3 text-green-500"></i>
                        Kas Harian
                    </h1>
                    <p class="text-gray-600 mt-1">
                        <i class="fas fa-calendar mr-2"></i>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($kasHarian && $kasHarian->status == 'Open')
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            STATUS: OPEN
                        </span>
                        <button onclick="showTutupKasModal()" 
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold flex items-center">
                            <i class="fas fa-lock mr-2"></i>
                            Tutup Kas
                        </button>
                    @else
                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold flex items-center">
                            <i class="fas fa-lock mr-2"></i>
                            STATUS: CLOSED
                        </span>
                        <a href="{{ route('kasir.kas-harian.index') }}" 
                           class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold flex items-center">
                            <i class="fas fa-lock-open mr-2"></i>
                            Buka Kas
                        </a>
                    @endif
                </div>
            </div>

            @if($kasHarian)
            <!-- Ringkasan Kas dalam Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <!-- Saldo Awal -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200 hover:shadow-md transition cursor-pointer" onclick="showDetailModal('saldo-awal')">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-gray-600"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500">SALDO AWAL</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($kasHarian->saldo_awal ?? 0, 0, ',', '.') }}</p>
                </div>

                <!-- Penerimaan Tunai -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl border border-blue-200 hover:shadow-md transition cursor-pointer" onclick="showDetailModal('penerimaan-tunai')">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-blue-600"></i>
                        </div>
                        <span class="text-xs font-semibold text-blue-600">PENERIMAAN TUNAI</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($kasHarian->penerimaan_tunai ?? 0, 0, ',', '.') }}</p>
                </div>

                <!-- Penerimaan Non-Tunai -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-xl border border-purple-200 hover:shadow-md transition cursor-pointer" onclick="showDetailModal('penerimaan-non-tunai')">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-credit-card text-purple-600"></i>
                        </div>
                        <span class="text-xs font-semibold text-purple-600">NON-TUNAI</span>
                    </div>
                    <p class="text-2xl font-bold text-purple-700">Rp {{ number_format($kasHarian->penerimaan_non_tunai ?? 0, 0, ',', '.') }}</p>
                </div>

                <!-- Total Penerimaan -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-xl border border-green-200 hover:shadow-md transition cursor-pointer" onclick="showDetailModal('total-penerimaan')">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-green-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-green-600"></i>
                        </div>
                        <span class="text-xs font-semibold text-green-600">TOTAL PENERIMAAN</span>
                    </div>
                    <p class="text-2xl font-bold text-green-700">Rp {{ number_format($kasHarian->total_penerimaan ?? 0, 0, ',', '.') }}</p>
                </div>

                <!-- Saldo Akhir -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 rounded-xl border border-indigo-200 hover:shadow-md transition cursor-pointer" onclick="showDetailModal('saldo-akhir')">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-balance-scale text-indigo-600"></i>
                        </div>
                        <span class="text-xs font-semibold text-indigo-600">SALDO AKHIR</span>
                    </div>
                    <p class="text-2xl font-bold text-indigo-700">Rp {{ number_format($kasHarian->saldo_akhir ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            @else
            <!-- Jika kas belum dibuka -->
            <div class="text-center py-8 bg-yellow-50 rounded-xl border border-yellow-200">
                <i class="fas fa-cash-register text-4xl text-yellow-500 mb-3"></i>
                <h3 class="text-xl font-bold text-yellow-800 mb-2">Kas Belum Dibuka</h3>
                <p class="text-yellow-600 mb-4">Buka kas terlebih dahulu untuk memulai transaksi hari ini</p>
                <a href="{{ route('kasir.kas-harian.index') }}" 
                   class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold inline-flex items-center">
                    <i class="fas fa-lock-open mr-2"></i>
                    Buka Kas Sekarang
                </a>
            </div>
            @endif
        </div>

        @if($kasHarian)
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Transaksi -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition cursor-pointer" onclick="window.location.href='{{ route('kasir.riwayat') }}'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_transaksi'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-receipt text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Penjualan -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition cursor-pointer" onclick="showDetailModal('total-penjualan')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_penjualan'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Transaksi Tunai -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition cursor-pointer" onclick="showDetailModal('transaksi-tunai')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Transaksi Tunai</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['transaksi_tunai'] ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-coins text-yellow-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Rata-rata Transaksi -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition cursor-pointer" onclick="showDetailModal('rata-rata')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rata-rata/Transaksi</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['rata_rata_transaksi'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calculator text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-clock mr-3 text-blue-500"></i>
                    Transaksi Terbaru
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ ($transaksiTerbaru ?? collect())->count() }} transaksi
                    </span>
                    <a href="{{ route('kasir.riwayat') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                        Lihat Semua →
                    </a>
                </div>
            </div>
            
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($transaksiTerbaru ?? [] as $transaksi)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition group cursor-pointer" onclick="showTransactionDetail({{ $transaksi->transaksi_id }})">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 group-hover:text-blue-600 transition">{{ $transaksi->no_transaksi }}</p>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="mr-3"><i class="fas fa-clock mr-1"></i>{{ $transaksi->tanggal_transaksi->format('H:i') }}</span>
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium">
                                {{ $transaksi->metode_pembayaran }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-600 text-lg">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">{{ $transaksi->items->count() }} items</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-receipt text-4xl mb-3"></i>
                    <p>Belum ada transaksi hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>

    <!-- QUICK ACTIONS MENU - Sidebar -->
    <div class="lg:w-80 space-y-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                Quick Actions
            </h3>
            
            <!-- Vertical Menu -->
            <div class="space-y-3">
                <!-- Transaksi Baru -->
                <a href="{{ route('kasir.index') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all duration-200 group">
                    <div class="bg-blue-500 text-white p-3 rounded-lg mr-4 group-hover:bg-blue-600 transition">
                        <i class="fas fa-shopping-cart text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">Transaksi Baru</p>
                        <p class="text-gray-600 text-sm">Mulai transaksi penjualan</p>
                    </div>
                    <i class="fas fa-arrow-right text-blue-500 opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-transform"></i>
                </a>

                <!-- Riwayat Transaksi -->
                <a href="{{ route('kasir.riwayat') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl hover:from-green-100 hover:to-green-200 transition-all duration-200 group">
                    <div class="bg-green-500 text-white p-3 rounded-lg mr-4 group-hover:bg-green-600 transition">
                        <i class="fas fa-history text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">Riwayat Transaksi</p>
                        <p class="text-gray-600 text-sm">Lihat history penjualan</p>
                    </div>
                    <i class="fas fa-arrow-right text-green-500 opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-transform"></i>
                </a>

                <!-- Daftar Produk -->
                <a href="{{ route('kasir.daftar-produk') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-xl hover:from-purple-100 hover:to-purple-200 transition-all duration-200 group">
                    <div class="bg-purple-500 text-white p-3 rounded-lg mr-4 group-hover:bg-purple-600 transition">
                        <i class="fas fa-boxes text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">Daftar Produk</p>
                        <p class="text-gray-600 text-sm">Cek stok produk</p>
                    </div>
                    <i class="fas fa-arrow-right text-purple-500 opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-transform"></i>
                </a>

                <!-- Laporan Harian -->
                <a href="{{ route('kasir.dashboard.laporan-harian') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-200 rounded-xl hover:from-orange-100 hover:to-orange-200 transition-all duration-200 group">
                    <div class="bg-orange-500 text-white p-3 rounded-lg mr-4 group-hover:bg-orange-600 transition">
                        <i class="fas fa-chart-bar text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">Laporan Harian</p>
                        <p class="text-gray-600 text-sm">Ringkasan penjualan</p>
                    </div>
                    <i class="fas fa-arrow-right text-orange-500 opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- Additional Info Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-indigo-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-indigo-500"></i>
                Info Hari Ini
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Shift Aktif:</span>
                    <span class="font-semibold text-green-600" id="current-shift">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Kasir:</span>
                    <span class="font-semibold">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status Kas:</span>
                    @if($kasHarian && $kasHarian->status == 'Open')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">OPEN</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">CLOSED</span>
                    @endif
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Waktu Server:</span>
                    <span class="font-semibold text-sm" id="server-time">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        @if($kasHarian && $kasHarian->status == 'Open')
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line mr-2 text-green-500"></i>
                Performance
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Rata-rata Transaksi:</span>
                    <span class="font-semibold">Rp {{ number_format(($stats['total_penjualan'] ?? 0) / max(($stats['total_transaksi'] ?? 1), 1), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Transaksi/Jam:</span>
                    <span class="font-semibold" id="transactions-per-hour">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Profit Margin:</span>
                    <span class="font-semibold text-green-600" id="profit-margin">-</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"></div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3 shadow-xl">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
        <span class="text-gray-700 font-medium">Memproses...</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    startServerTime();
});

function initializeDashboard() {
    // Add hover effects to all interactive cards
    document.querySelectorAll('.cursor-pointer').forEach(card => {
        card.classList.add('hover-lift');
    });
    
    updatePerformanceMetrics();
}

function startServerTime() {
    function updateTime() {
        const now = new Date();
        document.getElementById('server-time').textContent = 
            now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateTime();
    setInterval(updateTime, 1000);
}

function updatePerformanceMetrics() {
    // Update shift
    document.getElementById('current-shift').textContent = getCurrentShift();
    
    // Update transactions per hour
    const totalTransactions = {{ $stats['total_transaksi'] ?? 0 }};
    document.getElementById('transactions-per-hour').textContent = calculateTransactionsPerHour(totalTransactions);
    
    // Update profit margin
    const stats = @json($stats ?? []);
    document.getElementById('profit-margin').textContent = calculateProfitMargin(stats);
}

function getCurrentShift() {
    const hour = new Date().getHours();
    if (hour >= 6 && hour < 14) return 'Pagi';
    if (hour >= 14 && hour < 22) return 'Siang';
    return 'Malam';
}

function calculateTransactionsPerHour(totalTransactions) {
    const hour = new Date().getHours();
    const startHour = 8; // Store opens at 8 AM
    const operatingHours = Math.max(hour - startHour, 1);
    return (totalTransactions / operatingHours).toFixed(1);
}

function calculateProfitMargin(stats) {
    const revenue = stats.total_penjualan || 0;
    const profit = revenue; // Simplified - no expenses calculation
    return '100%'; // Simplified for now
}

// Placeholder functions for modal features
function showTutupKasModal() {
    alert('Fitur Tutup Kas akan segera tersedia');
}

function showDetailModal(type) {
    alert(`Detail ${type} akan ditampilkan di sini`);
}

function showTransactionDetail(transactionId) {
    alert(`Detail transaksi ${transactionId} akan ditampilkan`);
}

function showNotification(message, type = 'success', duration = 4000) {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-triangle',
        warning: 'exclamation-circle',
        info: 'info-circle'
    };
    
    const colors = {
        success: 'bg-green-500 border-green-600',
        error: 'bg-red-500 border-red-600',
        warning: 'bg-yellow-500 border-yellow-600',
        info: 'bg-blue-500 border-blue-600'
    };
    
    notification.className = `${colors[type]} text-white p-4 rounded-lg shadow-xl transform transition-all duration-300 translate-x-full opacity-0`;
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-${icons[type]} mr-3"></i>
                <span class="font-medium">${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
}
</script>

<style>
.hover-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endsection