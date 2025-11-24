@extends('layouts.kepala')

@section('title', 'Laporan Kas - Kepala')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laporan Kas</h1>
    <p class="text-gray-600">Monitor dan analisis laporan keuangan kas harian</p>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('kepala.laporan-kas') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex items-end space-x-2">
            <button type="submit" class="btn-kepala-primary">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('kepala.laporan-kas') }}" class="btn-kepala-secondary">
                <i class="fas fa-refresh mr-2"></i>Reset
            </a>
        </div>

        <div class="flex items-end justify-end">
            <a href="{{ route('kepala.laporan.export-kas') }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center mr-2">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-800" id="total-pendapatan">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-800" id="total-transaksi">
                    {{ number_format($totalTransaksi, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Rata-rata/Transaksi</p>
                <p class="text-2xl font-bold text-gray-800" id="rata-rata">
                    Rp {{ number_format($rataRata, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-calendar-day text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Hari Aktif</p>
                <p class="text-2xl font-bold text-gray-800" id="hari-aktif">
                    {{ $hariAktif }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Laporan Kas Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Detail Laporan Kas Harian</h2>
            <span class="text-sm text-gray-600">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pendapatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata/Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($laporanKasPaginated as $laporan)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('l') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-semibold">{{ $laporan->jumlah_transaksi }}</div>
                        <div class="text-xs text-gray-500">transaksi</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold {{ $laporan->total_pendapatan > 0 ? 'text-green-600' : 'text-gray-500' }}">
                            Rp {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            Rp {{ number_format($laporan->rata_rata, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('kepala.laporan-penjualan') }}?date={{ $laporan->tanggal }}"
                            class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada data transaksi untuk periode yang dipilih</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($laporanKasPaginated->isNotEmpty())
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">Total</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $totalTransaksi }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                        Rp {{ number_format($rataRata, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Pagination -->
    @if($laporanKasPaginated->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $laporanKasPaginated->links() }}
    </div>
    @endif
</div>

<!-- Chart Section -->
@if(isset($chartData) && $chartData->isNotEmpty())
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pendapatan Harian</h3>
        <div class="h-64">
            <canvas id="pendapatanChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Transaksi</h3>
        <div class="h-64">
            <canvas id="transaksiChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk charts - PERBAIKAN SYNTAX
    const labels = @json($chartData->pluck('tanggal_formatted'));
    const pendapatanData = @json($chartData->pluck('total_pendapatan'));
    const transaksiData = @json($chartData->pluck('jumlah_transaksi'));

    // Cek apakah elemen canvas ada sebelum membuat chart
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Pendapatan
        const pendapatanCtx = document.getElementById('pendapatanChart');
        if (pendapatanCtx) {
            new Chart(pendapatanCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Harian',
                        data: pendapatanData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        // Grafik Transaksi
        const transaksiCtx = document.getElementById('transaksiChart');
        if (transaksiCtx) {
            new Chart(transaksiCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: transaksiData,
                        backgroundColor: '#10b981',
                        borderColor: '#059669',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@else
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <div class="text-center text-gray-500 py-8">
        <i class="fas fa-chart-bar text-4xl mb-2 text-gray-300"></i>
        <p>Tidak ada data yang cukup untuk menampilkan grafik</p>
    </div>
</div>
@endif

<!-- Auto refresh setiap 30 detik untuk data real-time -->
<script>
    setTimeout(() => {
        window.location.reload();
    }, 30000);
</script>
@endsection