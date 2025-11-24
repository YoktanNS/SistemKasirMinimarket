@extends('layouts.kepala')

@section('title', 'Monitoring Kinerja Kasir - Kepala')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Monitoring Kinerja Kasir</h1>
    <p class="text-gray-600">Pantau performa dan produktivitas kasir</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Kasir</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($kasir->total(), 0, ',', '.') }}
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
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($totalTransaksi, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                <p class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi</p>
                <p class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($rataRata, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Periode -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Filter Periode</h3>
            <p class="text-sm text-gray-600">Data ditampilkan untuk {{ $monthName }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ request()->fullUrlWithQuery(['period' => 'month']) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition {{ request('period', 'month') == 'month' ? 'ring-2 ring-blue-300' : '' }}">
                Bulan Ini
            </a>
            <a href="{{ request()->fullUrlWithQuery(['period' => 'last_month']) }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition {{ request('period') == 'last_month' ? 'ring-2 ring-gray-300' : '' }}">
                Bulan Lalu
            </a>
            <a href="{{ request()->fullUrlWithQuery(['period' => 'all']) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition {{ request('period') == 'all' ? 'ring-2 ring-green-300' : '' }}">
                Semua Waktu
            </a>
        </div>
    </div>
</div>

<!-- Insight & Rekomendasi -->
@if($kasir->count() == 1)
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow p-6 mb-6 border border-blue-200">
    <div class="flex items-start">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
            <i class="fas fa-lightbulb text-xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Insight & Rekomendasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-medium text-blue-700 mb-1">📊 Status Performa</h4>
                    @php
                        $kasirPertama = $kasir->first();
                        $totalPenjualanKasir = $kasirPertama->total_penjualan ?? 0;
                        $totalTransaksiKasir = $kasirPertama->total_transaksi ?? 0;
                        $rataRataKasir = $totalTransaksiKasir > 0 ? $totalPenjualanKasir / $totalTransaksiKasir : 0;
                    @endphp
                    <p class="text-sm text-gray-700">
                        @if($totalPenjualanKasir >= 10000000)
                            🎉 <strong>Excellent!</strong> Kasir menunjukkan performa luar biasa.
                        @elseif($totalPenjualanKasir >= 5000000)
                            👍 <strong>Good!</strong> Kasir bekerja dengan baik.
                        @elseif($totalPenjualanKasir >= 1000000)
                            📈 <strong>Average.</strong> Ada ruang untuk peningkatan.
                        @else
                            💡 <strong>Needs Improvement.</strong> Perlu strategi peningkatan penjualan.
                        @endif
                    </p>
                </div>
                <div>
                    <h4 class="font-medium text-blue-700 mb-1">🚀 Rekomendasi</h4>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Fokus pada upselling produk bernilai tinggi</li>
                        <li>• Optimalkan layanan di jam sibuk</li>
                        <li>• Tingkatkan rata-rata transaksi ke Rp 30.000</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Kasir Performance Table -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Performa Kasir</h2>
            <span class="text-sm text-gray-600">
                Diurutkan berdasarkan total penjualan
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kasir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata/Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performa</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($kasir as $index => $user)
                @php
                    $totalPenjualanKasir = $user->total_penjualan ?? 0;
                    $totalTransaksiKasir = $user->total_transaksi ?? 0;
                    $rataRataKasir = $totalTransaksiKasir > 0 ? $totalPenjualanKasir / $totalTransaksiKasir : 0;
                    
                    // Tentukan performa berdasarkan total penjualan
                    if ($totalPenjualanKasir >= 10000000) {
                        $performa = 'Excellent';
                        $color = 'bg-green-100 text-green-800';
                    } elseif ($totalPenjualanKasir >= 5000000) {
                        $performa = 'Good';
                        $color = 'bg-blue-100 text-blue-800';
                    } elseif ($totalPenjualanKasir >= 1000000) {
                        $performa = 'Average';
                        $color = 'bg-yellow-100 text-yellow-800';
                    } else {
                        $performa = 'Needs Improvement';
                        $color = 'bg-orange-100 text-orange-800';
                    }
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                @if($index == 0) bg-yellow-100 text-yellow-800
                                @elseif($index == 1) bg-gray-100 text-gray-800
                                @elseif($index == 2) bg-orange-100 text-orange-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $index + 1 }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $user->nama }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $user->user_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-center text-blue-600">
                            {{ number_format($totalTransaksiKasir, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-green-600">
                            Rp {{ number_format($totalPenjualanKasir, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            Rp {{ number_format($rataRataKasir, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                            @if($performa == 'Excellent')
                                <i class="fas fa-star mr-1"></i>
                            @elseif($performa == 'Good')
                                <i class="fas fa-thumbs-up mr-1"></i>
                            @elseif($performa == 'Average')
                                <i class="fas fa-chart-line mr-1"></i>
                            @else
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                            @endif
                            {{ $performa }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada data kasir</p>
                        <p class="text-sm mt-2">Belum ada transaksi yang dicatat oleh kasir</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($kasir->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $kasir->links() }}
    </div>
    @endif
</div>

<!-- Charts Grid - VERSI DIPERBAIKI -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Trend Penjualan 7 Hari -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Penjualan 7 Hari Terakhir</h3>
        <div class="h-64">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
    
    <!-- Kategori Produk Terlaris -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Kategori Produk Terlaris</h3>
        <div class="h-64">
            <canvas id="kategoriChart"></canvas>
        </div>
    </div>
</div>

<!-- Jam Puncak Transaksi -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Jam Puncak Transaksi</h3>
    <div class="h-64">
        <canvas id="jamChart"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data untuk Trend Penjualan
const trendData = {
    labels: {!! json_encode(collect($trendPenjualan)->pluck('label')) !!},
    data: {!! json_encode(collect($trendPenjualan)->pluck('total')) !!}
};

// Data untuk Kategori Terlaris
const kategoriData = {
    labels: {!! json_encode($kategoriTerlaris->pluck('nama_kategori')) !!},
    data: {!! json_encode($kategoriTerlaris->pluck('total_penjualan')) !!},
    terjual: {!! json_encode($kategoriTerlaris->pluck('total_terjual')) !!}
};

// Data untuk Jam Puncak
const jamData = {
    labels: {!! json_encode($jamPuncak->pluck('jam')) !!},
    transaksi: {!! json_encode($jamPuncak->pluck('total_transaksi')) !!},
    penjualan: {!! json_encode($jamPuncak->pluck('total_penjualan')) !!}
};

// Trend Penjualan Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: trendData.labels,
        datasets: [{
            label: 'Total Penjualan',
            data: trendData.data,
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
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Total Penjualan (Rp)'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `Penjualan: Rp ${context.raw.toLocaleString('id-ID')}`;
                    }
                }
            }
        }
    }
});

// Kategori Terlaris Chart
const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
new Chart(kategoriCtx, {
    type: 'bar',
    data: {
        labels: kategoriData.labels,
        datasets: [{
            label: 'Total Penjualan',
            data: kategoriData.data,
            backgroundColor: [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Total Penjualan (Rp)'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const terjual = kategoriData.terjual[context.dataIndex];
                        return [
                            `Penjualan: Rp ${context.raw.toLocaleString('id-ID')}`,
                            `Terjual: ${terjual} unit`
                        ];
                    }
                }
            }
        }
    }
});

// Jam Puncak Chart
const jamCtx = document.getElementById('jamChart').getContext('2d');
new Chart(jamCtx, {
    type: 'bar',
    data: {
        labels: jamData.labels,
        datasets: [{
            label: 'Jumlah Transaksi',
            data: jamData.transaksi,
            backgroundColor: '#10b981',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jumlah Transaksi'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const penjualan = jamData.penjualan[context.dataIndex];
                        return [
                            `Transaksi: ${context.raw}`,
                            `Penjualan: Rp ${penjualan.toLocaleString('id-ID')}`
                        ];
                    }
                }
            }
        }
    }
});
</script>

<style>
.btn-kepala-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: none;
    transition: all 0.3s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}

.btn-kepala-primary:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-1px);
}
</style>
@endsection