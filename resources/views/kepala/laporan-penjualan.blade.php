@extends('layouts.kepala')

@section('title', 'Laporan Penjualan - Kepala')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laporan Penjualan</h1>
    <p class="text-gray-600">Monitor dan analisis data penjualan harian</p>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('kepala.laporan-penjualan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
            <a href="{{ route('kepala.laporan-penjualan') }}" class="btn-kepala-secondary">
                <i class="fas fa-refresh mr-2"></i>Reset
            </a>
        </div>

        <!-- Ganti bagian tombol export -->
        <div class="flex items-end justify-end">
            <a href="{{ route('kepala.laporan.export-penjualan') }}?start_date={{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}&end_date={{ request('end_date', now()->format('Y-m-d')) }}"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </form>

    <!-- Debug Info -->
    @if(config('app.debug'))
    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
        <p class="text-sm text-yellow-800">
            <strong>Debug Info:</strong> Menampilkan data dari {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} hingga {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </p>
    </div>
    @endif
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                <p class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($summary['total_transaksi'], 0, ',', '.') }}
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
                <p class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($summary['rata_rata'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-cubes text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Item Terjual</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($summary['total_item'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Tabel 5 Produk Terlaris -->
@if($produkTerlaris->isNotEmpty())
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">5 Produk Terlaris</h2>
            <span class="text-sm text-gray-600">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Terjual</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata/Harga</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($produkTerlaris as $index => $produk)
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
                        <div class="text-sm font-medium text-gray-900">{{ $produk->nama_produk }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 text-center">{{ number_format($produk->total_terjual, 0, ',', '.') }} item</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-green-600">
                            Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">
                            @php
                            $rataHarga = $produk->total_terjual > 0 ? $produk->total_penjualan / $produk->total_terjual : 0;
                            @endphp
                            Rp {{ number_format($rataHarga, 0, ',', '.') }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            @if($produkTerlaris->isNotEmpty())
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900" colspan="2">Total</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-center">
                        {{ number_format($produkTerlaris->sum('total_terjual'), 0, ',', '.') }} item
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600">
                        Rp {{ number_format($produkTerlaris->sum('total_penjualan'), 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                        @php
                        $totalTerjual = $produkTerlaris->sum('total_terjual');
                        $totalPenjualan = $produkTerlaris->sum('total_penjualan');
                        $rataRataHarga = $totalTerjual > 0 ? $totalPenjualan / $totalTerjual : 0;
                        @endphp
                        Rp {{ number_format($rataRataHarga, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endif

<!-- Laporan Penjualan Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Detail Transaksi Penjualan</h2>
            <span class="text-sm text-gray-600">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Bayar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bayar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transaksi as $trx)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $trx->no_transaksi }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $trx->transaksi_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d F Y') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $trx->kasir->nama ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $trx->kasir->username ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($trx->metode_pembayaran === 'Tunai') bg-green-100 text-green-800
                            @elseif($trx->metode_pembayaran === 'Debit') bg-blue-100 text-blue-800
                            @elseif($trx->metode_pembayaran === 'QRIS') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $trx->metode_pembayaran }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 text-center">{{ $trx->total_item }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            Rp {{ number_format($trx->subtotal, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-red-600">
                            - Rp {{ number_format($trx->diskon, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-green-600">
                            Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('kepala.transaksi.detail', $trx->transaksi_id) }}"
                                class="text-blue-600 hover:text-blue-900"
                                title="Detail Transaksi">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('kepala.transaksi.struk', $trx->transaksi_id) }}"
                                target="_blank"
                                class="text-green-600 hover:text-green-900"
                                title="Cetak Struk">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-receipt text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada data transaksi untuk periode yang dipilih</p>
                        <p class="text-sm mt-2">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($transaksi->isNotEmpty())
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900" colspan="4">Total</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-center">
                        {{ $summary['total_item'] }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                        Rp {{ number_format($transaksi->sum('subtotal'), 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-red-600">
                        - Rp {{ number_format($transaksi->sum('diskon'), 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600">
                        Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Pagination -->
    @if($transaksi->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $transaksi->links() }}
    </div>
    @endif
</div>

<!-- Chart Section -->
@if(isset($transaksiForChart) && $transaksiForChart->isNotEmpty() && $produkTerlaris->isNotEmpty())
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">5 Top Barang Terlaris</h3>
        <div class="h-64">
            <canvas id="produkTerlarisChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Metode Pembayaran</h3>
        <div class="h-64">
            <canvas id="pembayaranChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk charts - PERBAIKAN SYNTAX
    const pembayaranData = @json($metodePembayaranData);

    // Data produk terlaris dari database
    const produkTerlarisData = {
        labels: @json($produkTerlaris->pluck('nama_produk')),
        data: @json($produkTerlaris->pluck('total_terjual')),
        penjualan: @json($produkTerlaris->pluck('total_penjualan'))
    };

    // Cek apakah elemen canvas ada sebelum membuat chart
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Produk Terlaris
        const produkTerlarisCtx = document.getElementById('produkTerlarisChart');
        if (produkTerlarisCtx) {
            new Chart(produkTerlarisCtx, {
                type: 'bar',
                data: {
                    labels: produkTerlarisData.labels,
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: produkTerlarisData.data,
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444'
                        ],
                        borderWidth: 1
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
                                afterLabel: function(context) {
                                    const index = context.dataIndex;
                                    return 'Total Penjualan: Rp ' + produkTerlarisData.penjualan[index].toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Terjual'
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }

        // Grafik Metode Pembayaran
        const pembayaranCtx = document.getElementById('pembayaranChart');
        if (pembayaranCtx) {
            new Chart(pembayaranCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(pembayaranData),
                    datasets: [{
                        data: Object.values(pembayaranData),
                        backgroundColor: [
                            '#10b981',
                            '#3b82f6',
                            '#8b5cf6',
                            '#6b7280'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });

    // Debug info untuk chart
    console.log('Chart Data Debug:');
    console.log('Produk Terlaris:', produkTerlarisData);
    console.log('Metode Pembayaran:', pembayaranData);
</script>
@else
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <div class="text-center text-gray-500 py-8">
        <i class="fas fa-chart-bar text-4xl mb-2 text-gray-300"></i>
        <p>Tidak ada data yang cukup untuk menampilkan grafik</p>
    </div>
</div>
@endif

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

    .btn-kepala-secondary {
        background: #6b7280;
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

    .btn-kepala-secondary:hover {
        background: #4b5563;
        transform: translateY(-1px);
    }
</style>
@endsection