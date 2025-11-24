@extends('layouts.kepala')

@section('title', 'Laporan Stok - Kepala')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laporan Stok</h1>
    <p class="text-gray-600">Monitor dan kelola data stok produk</p>
</div>

<!-- Filter Section dengan Tombol Export -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <!-- Form Filter -->
        <form id="filter-form" action="{{ route('kepala.laporan-stok') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="kategori" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriProduk as $kategori)
                    <option value="{{ $kategori->kategori_id }}" {{ request('kategori') == $kategori->kategori_id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Stok</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Status</option>
                    <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama Z-A</option>
                    <option value="stok_asc" {{ request('sort') == 'stok_asc' ? 'selected' : '' }}>Stok Terendah</option>
                    <option value="stok_desc" {{ request('sort') == 'stok_desc' ? 'selected' : '' }}>Stok Tertinggi</option>
                    <option value="nilai_desc" {{ request('sort') == 'nilai_desc' ? 'selected' : '' }}>Nilai Tertinggi</option>
                </select>
            </div>

            <!-- Tombol dalam form untuk mobile view -->
            <div class="md:hidden flex gap-2">
                <button type="submit" class="btn-kepala-primary flex items-center justify-center flex-1">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('kepala.laporan-stok') }}" class="btn-kepala-secondary flex items-center justify-center flex-1">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
            </div>
        </form>

        <!-- Action Buttons untuk desktop -->
        <div class="hidden md:flex flex-col sm:flex-row gap-3">
            <div class="flex gap-2">
                <button type="submit" form="filter-form" class="btn-kepala-primary flex items-center justify-center min-w-[120px]">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('kepala.laporan-stok') }}" class="btn-kepala-secondary flex items-center justify-center min-w-[120px]">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
            </div>
            <a href="{{ route('kepala.laporan.export-stok') }}?{{ http_build_query(request()->query()) }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center justify-center min-w-[140px] shadow-md hover:shadow-lg">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Info Filter Aktif -->
    @if(request()->anyFilled(['kategori', 'status', 'sort']))
    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
            <span class="text-sm font-medium text-blue-800 mr-3">Filter Aktif:</span>
            <div class="flex flex-wrap gap-2">
                @if(request('kategori'))
                    @php
                        $kategoriAktif = $kategoriProduk->where('kategori_id', request('kategori'))->first();
                    @endphp
                    <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs">
                        <i class="fas fa-tag mr-1"></i>
                        Kategori: {{ $kategoriAktif->nama_kategori ?? 'Tidak Diketahui' }}
                    </span>
                @endif
                @if(request('status'))
                    @php
                        $statusColors = [
                            'aman' => 'bg-green-100 text-green-800',
                            'menipis' => 'bg-orange-100 text-orange-800', 
                            'habis' => 'bg-red-100 text-red-800'
                        ];
                    @endphp
                    <span class="inline-flex items-center {{ $statusColors[request('status')] }} px-3 py-1 rounded-full text-xs">
                        <i class="fas fa-{{ request('status') == 'aman' ? 'check' : (request('status') == 'menipis' ? 'exclamation-triangle' : 'times') }} mr-1"></i>
                        Status: {{ ucfirst(request('status')) }}
                    </span>
                @endif
                @if(request('sort'))
                    @php
                        $sortLabels = [
                            'nama_asc' => 'Nama A-Z',
                            'nama_desc' => 'Nama Z-A', 
                            'stok_asc' => 'Stok Terendah',
                            'stok_desc' => 'Stok Tertinggi',
                            'nilai_desc' => 'Nilai Tertinggi'
                        ];
                    @endphp
                    <span class="inline-flex items-center bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs">
                        <i class="fas fa-sort mr-1"></i>
                        Urutan: {{ $sortLabels[request('sort')] ?? 'Default' }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-boxes text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Produk</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($summary['total_produk'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- TAMBAHKAN CARD TOTAL STOK SEMUA -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-cubes text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Stok Semua</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($summary['total_stok_semua'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Stok Aman</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($summary['stok_aman'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Stok Menipis</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($summary['stok_menipis_count'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Stok Habis</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($summary['stok_habis'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Total Nilai Stok & Total Stok Semua -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white text-sm font-medium">Total Nilai Stok</p>
                <p class="text-3xl font-bold text-white mt-2">
                    Rp {{ number_format($summary['total_nilai_stok'], 0, ',', '.') }}
                </p>
                <p class="text-blue-100 text-sm mt-2">
                    Nilai total semua stok produk berdasarkan harga jual
                </p>
            </div>
            <div class="p-4 rounded-full bg-white bg-opacity-20">
                <i class="fas fa-money-bill-wave text-3xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- TAMBAHKAN CARD TOTAL STOK SEMUA YANG BESAR -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white text-sm font-medium">Total Semua Stok Barang</p>
                <p class="text-3xl font-bold text-white mt-2">
                    {{ number_format($summary['total_stok_semua'], 0, ',', '.') }} item
                </p>
                <p class="text-indigo-100 text-sm mt-2">
                    Jumlah total semua stok barang yang tersedia
                </p>
            </div>
            <div class="p-4 rounded-full bg-white bg-opacity-20">
                <i class="fas fa-cubes text-3xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stok Menipis Section -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Stok Menipis</h2>
            <span class="text-sm text-red-600 font-medium">
                Perlu Perhatian: {{ $stokMenipis->count() }} produk
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-red-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Stok Tersedia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Stok Minimum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stokMenipis as $produk)
                <tr class="hover:bg-red-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $produk->nama_produk }}</div>
                        <div class="text-xs text-gray-500">SKU: {{ $produk->sku ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $produk->kategori->nama_kategori ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $produk->supplier->nama_supplier ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-red-600 text-center">
                            {{ number_format($produk->stok_tersedia, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 text-center">
                            {{ number_format($produk->stok_minimum, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($produk->stok_tersedia == 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Habis
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Menipis
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-2 text-green-300"></i>
                        <p>Tidak ada stok yang menipis</p>
                        <p class="text-sm mt-2">Semua stok dalam kondisi aman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Produk dengan Nilai Stok Tertinggi Section -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">10 Produk dengan Nilai Stok Tertinggi</h2>
            <span class="text-sm text-gray-600">
                Berdasarkan nilai investasi stok
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Tersedia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Stok</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($produkNilaiTertinggi as $index => $produk)
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
                        <div class="text-xs text-gray-500">SKU: {{ $produk->sku ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-center
                            @if($produk->stok_tersedia <= $produk->stok_minimum) text-red-600
                            @else text-green-600 @endif">
                            {{ number_format($produk->stok_tersedia, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-purple-600">
                            Rp {{ number_format($produk->total_nilai, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($produk->stok_tersedia == 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Habis
                            </span>
                        @elseif($produk->stok_tersedia <= $produk->stok_minimum)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Menipis
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Aman
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-chart-line text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada data produk</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Stok Per Kategori Section -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Stok Berdasarkan Kategori</h2>
            <span class="text-sm text-gray-600">
                Distribusi stok per kategori produk
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stokPerKategori as $kategori)
                @php
                    $percentage = ($kategori->total_nilai_stok / $summary['total_nilai_stok']) * 100;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $kategori->nama_kategori }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 text-center">
                            {{ number_format($kategori->jumlah_produk, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 text-center">
                            {{ number_format($kategori->total_stok, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-blue-600">
                            Rp {{ number_format($kategori->total_nilai_stok, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">{{ number_format($percentage, 1) }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-tags text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada data kategori</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Grafik Stok -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Status Stok</h3>
        <div class="h-64">
            <canvas id="statusStokChart"></canvas>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Kategori Produk</h3>
        <div class="h-64">
            <canvas id="kategoriChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data untuk grafik status stok
const statusStokData = {
    labels: ['Stok Aman', 'Stok Menipis', 'Stok Habis'],
    data: [{{ $summary['stok_aman'] }}, {{ $summary['stok_menipis_count'] }}, {{ $summary['stok_habis'] }}],
    colors: ['#10b981', '#f59e0b', '#ef4444']
};

// Data untuk grafik kategori
const kategoriData = {
    labels: {!! json_encode($kategoriProduk->pluck('nama_kategori')) !!},
    data: {!! json_encode($kategoriProduk->pluck('total_produk')) !!},
    colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1']
};

// Grafik Status Stok
const statusStokCtx = document.getElementById('statusStokChart').getContext('2d');
new Chart(statusStokCtx, {
    type: 'doughnut',
    data: {
        labels: statusStokData.labels,
        datasets: [{
            data: statusStokData.data,
            backgroundColor: statusStokData.colors,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label;
                        const value = context.raw;
                        const total = {{ $summary['total_produk'] }};
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} produk (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Grafik Kategori
const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
new Chart(kategoriCtx, {
    type: 'bar',
    data: {
        labels: kategoriData.labels,
        datasets: [{
            label: 'Jumlah Produk',
            data: kategoriData.data,
            backgroundColor: kategoriData.colors,
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
                    text: 'Jumlah Produk'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `Jumlah Produk: ${context.raw}`;
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