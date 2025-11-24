@extends('layouts.kepala')

@section('title', 'Dashboard Kepala')
@section('page_title', '📊 Dashboard Kepala Minimarket')

@section('content')
<div class="space-y-6">
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Penjualan Hari Ini -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($penjualan_hari_ini, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $total_transaksi_hari_ini }} transaksi</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stok Menipis</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stok_menipis }} produk</p>
                    <p class="text-sm text-gray-500 mt-1">Perlu restock</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Produk -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Produk</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $total_produk }} item</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $produk_habis }} habis</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-boxes text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Status Kas -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Kas Harian</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2 capitalize">{{ $status_kas }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($kas_hari_ini)
                            Rp {{ number_format($kas_hari_ini->saldo_akhir, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-cash-register text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('kepala.laporan-penjualan') }}" class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg text-center transition">
                <i class="fas fa-chart-line text-blue-600 text-2xl mb-2"></i>
                <p class="font-medium text-blue-700">Laporan Penjualan</p>
            </a>
            <a href="{{ route('kepala.laporan-kas') }}" class="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center transition">
                <i class="fas fa-money-bill-wave text-green-600 text-2xl mb-2"></i>
                <p class="font-medium text-green-700">Laporan Kas</p>
            </a>
            <a href="{{ route('kepala.laporan-stok') }}" class="bg-red-50 hover:bg-red-100 p-4 rounded-lg text-center transition">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl mb-2"></i>
                <p class="font-medium text-red-700">Stok Menipis</p>
            </a>
            <a href="{{ route('kepala.monitoring.kasir') }}" class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg text-center transition">
                <i class="fas fa-list-alt text-purple-600 text-2xl mb-2"></i>
                <p class="font-medium text-purple-700">Monitoring Kasir</p>
            </a>
        </div>
    </div>
</div>
@endsection