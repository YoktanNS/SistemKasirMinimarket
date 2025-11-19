@extends('layouts.app')

@section('title', 'Laporan Manajerial')
@section('page_title', '📊 Laporan Manajerial')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white shadow-lg rounded-2xl p-6">
    <!-- Header dengan Filter -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-wide">📈 Laporan Manajerial</h2>
            <p class="text-gray-500 text-sm mt-1">Analisis bisnis komprehensif dan insight performa</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <!-- Filter Periode -->
            <form method="GET" class="flex flex-wrap gap-3">
                <select name="periode" onchange="this.form.submit()"
                    class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="hari-ini" {{ request('periode') == 'hari-ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="kemarin" {{ request('periode') == 'kemarin' ? 'selected' : '' }}>Kemarin</option>
                    <option value="minggu-ini" {{ request('periode') == 'minggu-ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan-ini" {{ request('periode') == 'bulan-ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="custom" {{ request('periode') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>

                @if(request('periode') == 'custom')
                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal', now()->format('Y-m-d')) }}"
                    class="bg-white border border-gray-300 rounded-lg px-4 py-2">
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', now()->format('Y-m-d')) }}"
                    class="bg-white border border-gray-300 rounded-lg px-4 py-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Terapkan
                </button>
                @endif
            </form>

            <!-- Tombol Aksi -->
            <div class="flex gap-2">
                <!-- Tombol Cetak Quick PDF -->
                <form action="{{ route('admin.laporan.cetak-quick') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="periode" value="{{ request('periode', 'hari-ini') }}">
                    <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                    <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-md transition flex items-center gap-2 font-semibold">
                        <i class="fas fa-file-pdf"></i> Cetak Quick PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Periode Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="text-2xl">📅</div>
                <div>
                    <p class="font-semibold text-blue-800">Periode Laporan: {{ $statistik['periode_text'] ?? 'Hari Ini' }}</p>
                    <p class="text-sm text-blue-600">Data diambil dari transaksi yang sudah selesai</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-600">Total Profit Bersih</p>
                <p class="text-xl font-bold text-green-600">Rp{{ number_format($statistik['net_profit'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Ringkasan Performa -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Penjualan</p>
                    <p class="text-2xl font-bold mt-2">Rp{{ number_format($statistik['total_penjualan'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="text-3xl">💰</div>
            </div>
            <p class="text-xs opacity-80 mt-2">{{ $statistik['total_transaksi'] ?? 0 }} transaksi</p>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Profit Kotor</p>
                    <p class="text-2xl font-bold mt-2">Rp{{ number_format($statistik['total_profit'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="text-3xl">📈</div>
            </div>
            <p class="text-xs opacity-80 mt-2">
                @php
                    $margin = ($statistik['total_penjualan'] ?? 0) > 0 ? 
                        number_format((($statistik['total_profit'] ?? 0) / ($statistik['total_penjualan'] ?? 1)) * 100, 1) : 0;
                @endphp
                Margin: {{ $margin }}%
            </p>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Transaksi Tunai</p>
                    <p class="text-2xl font-bold mt-2">{{ $statistik['transaksi_tunai'] ?? 0 }}</p>
                </div>
                <div class="text-3xl">💵</div>
            </div>
            <p class="text-xs opacity-80 mt-2">{{ $statistik['transaksi_non_tunai'] ?? 0 }} non-tunai</p>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Rata-rata/Transaksi</p>
                    <p class="text-2xl font-bold mt-2">Rp{{ number_format($statistik['rata_rata_transaksi'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
            <p class="text-xs opacity-80 mt-2">Net: Rp{{ number_format($statistik['net_profit'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Grid Konten -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Produk Terlaris -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-fire text-orange-500"></i> Produk Terlaris
                <span class="text-sm text-gray-500">(Top 5)</span>
            </h3>
            
            @if($produkTerlaris->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terjual</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($produkTerlaris as $produk)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $produk->nama_produk }}</div>
                                <div class="text-xs text-gray-500">
                                    @ Rp{{ number_format($produk->harga_jual, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-blue-600">
                                {{ $produk->total_terjual }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">
                                Rp{{ number_format($produk->total_pendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center text-gray-500 py-8">
                <div class="text-4xl mb-2">📦</div>
                <p>Tidak ada data produk terlaris</p>
                <p class="text-sm">Belum ada transaksi pada periode ini</p>
            </div>
            @endif
        </div>

        <!-- Performa Kasir -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-blue-500"></i> Performa Kasir
            </h3>
            
            @if($performansiKasir->count() > 0)
            <div class="space-y-4">
                @foreach($performansiKasir as $kasir)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div>
                        <div class="font-medium text-gray-900">{{ $kasir->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500">{{ $kasir->total_transaksi }} transaksi</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-green-600">
                            Rp{{ number_format($kasir->total_penjualan, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            @php
                                $rataRata = $kasir->total_transaksi > 0 ? $kasir->total_penjualan / $kasir->total_transaksi : 0;
                            @endphp
                            Rp{{ number_format($rataRata, 0, ',', '.') }}/transaksi
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center text-gray-500 py-8">
                <div class="text-4xl mb-2">👨‍💼</div>
                <p>Tidak ada data performa kasir</p>
                <p class="text-sm">Belum ada transaksi pada periode ini</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Analisis Keuangan Sederhana -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar text-purple-500"></i> Analisis Keuangan
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $statistik['total_transaksi'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600">Total Transaksi</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">
                    Rp{{ number_format($statistik['net_profit'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-600">Profit Bersih</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">
                    {{ number_format($statistik['total_profit'] ?? 0, 0, ',', '.') > 0 ? 
                       number_format((($statistik['net_profit'] ?? 0) / ($statistik['total_profit'] ?? 1)) * 100, 1) : 0 }}%
                </div>
                <div class="text-sm text-gray-600">Profit Margin</div>
            </div>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-2 flex items-center gap-2">
            <i class="fas fa-lightbulb text-yellow-500"></i> Tips Penggunaan
        </h3>
        <ul class="text-sm text-yellow-700 space-y-1">
            <li>• <strong>Harian:</strong> Untuk laporan shift & briefing tim</li>
            <li>• <strong>Mingguan:</strong> Untuk evaluasi performa mingguan</li>
            <li>• <strong>Bulanan:</strong> Untuk laporan ke manajemen</li>
            <li>• <strong>Custom:</strong> Untuk periode khusus (promo, event)</li>
            <li>• <strong>Cetak Quick PDF:</strong> Untuk kebutuhan instan meeting atau dokumentasi</li>
        </ul>
    </div>
</div>
@endsection