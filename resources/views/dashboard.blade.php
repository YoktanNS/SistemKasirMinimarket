@extends('layout')

@section('title', 'MIS - Dashboard')

@section('header', 'Dashboard MIS - Laporan Penjualan & Analisis Kinerja')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Penjualan Hari Ini</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">Rp {{ number_format($kpi['penjualanHariIni'], 0, ',', '.') }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="{{ $kpi['persenHariIni'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                        {{ $kpi['persenHariIni'] >= 0 ? '▲' : '▼' }} {{ number_format(abs($kpi['persenHariIni']), 1) }}%
                    </span>
                    <span class="text-gray-600"> vs kemarin</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Penjualan Minggu Ini</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">Rp {{ number_format($kpi['penjualanMingguIni'], 0, ',', '.') }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
             <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="{{ $kpi['persenMingguIni'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                        {{ $kpi['persenMingguIni'] >= 0 ? '▲' : '▼' }} {{ number_format(abs($kpi['persenMingguIni']), 1) }}%
                    </span>
                    <span class="text-gray-600"> vs minggu lalu</span>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
             <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                         <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Penjualan Bulan Ini</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">Rp {{ number_format($kpi['penjualanBulanIni'], 0, ',', '.') }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
             <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="{{ $kpi['persenBulanIni'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                        {{ $kpi['persenBulanIni'] >= 0 ? '▲' : '▼' }} {{ number_format(abs($kpi['persenBulanIni']), 1) }}%
                    </span>
                    <span class="text-gray-600"> vs bulan lalu</span>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h15.75c.621 0 1.125.504 1.125 1.125v6.75C21 20.496 20.496 21 19.875 21H4.125A1.125 1.125 0 0 1 3 19.875v-6.75ZM3 9.75C3 9.129 3.504 8.625 4.125 8.625h15.75c.621 0 1.125.504 1.125 1.125v.375c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 10.125v-.375ZM3 6.375C3 5.754 3.504 5.25 4.125 5.25h15.75c.621 0 1.125.504 1.125 1.125v.375c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 6.75v-.375Z" /></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Transaksi Hari Ini</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $kpi['transaksiHariIni'] }} Transaksi</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-gray-600">Rata-rata: </span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($kpi['avgTransaksi'], 0, ',', '.') }}/transaksi</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Produk Terlaris (Bulan Ini)</h3>
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($topProducts as $index => $item)
                <li class="py-3 flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="text-lg font-bold text-gray-400 mr-3">#{{ $index + 1 }}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $item->product->product_name ?? 'Produk Dihapus' }}</p>
                            <p class="text-sm text-gray-500">{{ $item->total_terjual }} unit terjual</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-blue-600">Rp {{ number_format($item->total_rupiah, 0, ',', '.') }}</span>
                </li>
                @empty
                <li class="py-3 text-sm text-gray-500">Belum ada data penjualan bulan ini.</li>
                @endforelse
            </ul>
        </div>

        <div class="lg:col-span-1 bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notifikasi Stok & Alert</h3>
            <div class="space-y-3">
                @forelse($stokMenipis as $item)
                <div class="p-3 rounded-lg bg-red-50 border border-red-200">
                    <p class="text-sm font-medium text-red-800">Stok Menipis</p>
                    <p class="text-sm text-red-700">{{ $item->product_name }} - <span class="font-bold">Sisa {{ $item->stock }} unit</span></p>
                </div>
                @empty
                <div class="p-3 rounded-lg bg-green-50 border border-green-200">
                    <p class="text-sm font-medium text-green-800">Stok Aman</p>
                    <p class="text-sm text-green-700">Semua stok produk di atas batas minimum.</p>
                </div>
                @endforelse
                </div>
        </div>
        
        <div class="lg:col-span-1 bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Analisis Penjualan per Kategori</h3>
            <div class="h-64">
                <canvas id="kategoriChart"></canvas>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    // Import Chart.js
    import Chart from 'chart.js/auto';

    // Ambil data dari Controller (di-encode sebagai JSON)
    const kategoriData = @json($kategoriSales);

    // Siapkan data untuk chart
    const labels = kategoriData.map(item => item.category);
    const dataValues = kategoriData.map(item => item.total_penjualan);
    const backgroundColors = [
        '#3B82F6', // blue-500
        '#10B981', // emerald-500
        '#F59E0B', // amber-500
        '#EF4444', // red-500
        '#8B5CF6', // violet-500
    ];

    // Dapatkan elemen canvas
    const ctx = document.getElementById('kategoriChart').getContext('2d');
    
    // Buat chart baru
    new Chart(ctx, {
        type: 'doughnut', // Jenis chart: donat
        data: {
            labels: labels,
            datasets: [{
                label: 'Penjualan per Kategori',
                data: dataValues,
                backgroundColor: backgroundColors,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom', // Pindahkan legenda ke bawah
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed;
                            let total = context.chart.getDatasetMeta(0).total;
                            let percentage = ((value / total) * 100).toFixed(1) + '%';
                            return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage})`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush