<!-- resources/views/kasir/tutup-kas.blade.php -->
@extends('layouts.kasir')

@section('title', 'Tutup Kas Harian - SmartMart Campus')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-lock mr-3 text-red-500"></i>
                    Tutup Kas Harian
                </h1>
                <p class="text-gray-600 mt-1">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <a href="{{ route('kasir.dashboard') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <!-- Alert Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                        <div>
                            <p class="font-semibold text-blue-800">Informasi Tutup Kas</p>
                            <p class="text-blue-700 text-sm mt-1">
                                Pastikan semua transaksi hari ini sudah selesai dan tercatat dengan benar sebelum menutup kas.
                                Setelah kas ditutup, tidak dapat dibuka kembali untuk hari ini.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-0.5"></i>
                        <div>
                            <p class="font-semibold text-yellow-800">Perhatian!</p>
                            <p class="text-yellow-700 text-sm mt-1">
                                Proses ini akan mengunci kas hari ini. Pastikan saldo fisik sesuai dengan sistem.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Kas -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-gray-600"></i>
                        Ringkasan Kas Hari Ini
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                                <span class="text-gray-600">Saldo Awal:</span>
                                <span class="font-bold text-gray-800">Rp {{ number_format($kasHarian->saldo_awal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                                <span class="text-gray-600">Penerimaan Tunai:</span>
                                <span class="font-bold text-green-600">Rp {{ number_format($kasHarian->penerimaan_tunai, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                                <span class="text-gray-600">Penerimaan Non-Tunai:</span>
                                <span class="font-bold text-blue-600">Rp {{ number_format($kasHarian->penerimaan_non_tunai, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                                <span class="text-gray-600">Total Penerimaan:</span>
                                <span class="font-bold text-purple-600">
                                    Rp {{ number_format($kasHarian->penerimaan_tunai + $kasHarian->penerimaan_non_tunai, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-2 border-indigo-200">
                                <span class="text-gray-800 font-semibold">Saldo Akhir:</span>
                                <span class="font-bold text-indigo-600 text-lg">
                                    Rp {{ number_format($kasHarian->saldo_akhir, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Tutup Kas -->
                <form id="tutupKasForm" action="{{ route('kasir.kas-harian.tutup') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="keterangan_tutup" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan Penutupan Kas:
                        </label>
                        <textarea 
                            id="keterangan_tutup" 
                            name="keterangan"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                            placeholder="Contoh: Kas ditutup sesuai prosedur, semua transaksi sudah selesai, saldo fisik sesuai..."
                            required
                        ></textarea>
                        <p class="text-sm text-gray-500 mt-1">Berikan keterangan mengenai penutupan kas hari ini</p>
                    </div>

                    <!-- Konfirmasi -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-red-500 mr-3 mt-0.5"></i>
                            <div>
                                <p class="font-semibold text-red-800">Konfirmasi Final</p>
                                <p class="text-red-700 text-sm mt-1">
                                    Saya telah memverifikasi bahwa semua data sudah benar dan siap untuk menutup kas hari ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('kasir.dashboard') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Batalkan
                        </a>
                        <button 
                            type="submit"
                            id="submitButton"
                            class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium flex items-center"
                        >
                            <i class="fas fa-lock mr-2"></i>
                            Konfirmasi & Tutup Kas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Info Kasir -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-500"></i>
                    Informasi Kasir
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Nama Kasir:</span>
                        <span class="font-semibold">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Shift:</span>
                        <span class="font-semibold text-green-600" id="current-shift">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Waktu:</span>
                        <span class="font-semibold text-sm" id="current-time">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistik Hari Ini -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-green-500"></i>
                    Statistik Hari Ini
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Transaksi:</span>
                        <span class="font-semibold">{{ $stats['total_transaksi'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Penjualan:</span>
                        <span class="font-semibold">Rp {{ number_format($stats['total_penjualan'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Transaksi Tunai:</span>
                        <span class="font-semibold">{{ $stats['transaksi_tunai'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Rata-rata/Transaksi:</span>
                        <span class="font-semibold">Rp {{ number_format($stats['rata_rata_transaksi'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Cek Sebelum Tutup
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('kasir.riwayat') }}" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-history mr-3 text-blue-500"></i>
                        Lihat Riwayat Transaksi
                    </a>
                    <a href="{{ route('kasir.dashboard.laporan-harian') }}" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-file-alt mr-3 text-green-500"></i>
                        Preview Laporan Harian
                    </a>
                    <button onclick="refreshKasData()" 
                       class="w-full flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-sync-alt mr-3 text-purple-500"></i>
                        Refresh Data Kas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3 shadow-xl">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-500"></div>
        <span class="text-gray-700 font-medium">Menutup Kas...</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeTutupKasPage();
});

function initializeTutupKasPage() {
    startServerTime();
    updateShiftInfo();
    
    // Form submission handler
    document.getElementById('tutupKasForm').addEventListener('submit', function(e) {
        e.preventDefault();
        konfirmasiTutupKas();
    });
}

function startServerTime() {
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = 
            now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateTime();
    setInterval(updateTime, 1000);
}

function updateShiftInfo() {
    document.getElementById('current-shift').textContent = getCurrentShift();
}

function getCurrentShift() {
    const hour = new Date().getHours();
    if (hour >= 6 && hour < 14) return 'Pagi';
    if (hour >= 14 && hour < 22) return 'Siang';
    return 'Malam';
}

function refreshKasData() {
    // Implement refresh data jika diperlukan
    window.location.reload();
}

function konfirmasiTutupKas() {
    const keterangan = document.getElementById('keterangan_tutup').value.trim();
    
    if (!keterangan) {
        alert('Harap isi keterangan penutupan kas');
        document.getElementById('keterangan_tutup').focus();
        return;
    }
    
    if (!confirm('Apakah Anda yakin ingin menutup kas hari ini? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    // Show loading
    const submitButton = document.getElementById('submitButton');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    showLoading();
    
    // Submit form secara native (bukan AJAX) agar bisa redirect
    document.getElementById('tutupKasForm').submit();
}

function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('loadingOverlay').classList.add('flex');
}

// Handle back button
window.addEventListener('beforeunload', function (e) {
    const submitButton = document.getElementById('submitButton');
    if (submitButton.disabled) {
        e.preventDefault();
        e.returnValue = 'Proses tutup kas sedang berjalan. Apakah Anda yakin ingin meninggalkan halaman ini?';
    }
});
</script>

<style>
.hover-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 5px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection