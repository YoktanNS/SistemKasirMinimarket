@extends('layouts.kepala')

@section('title', 'Detail Transaksi - Kepala')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Transaksi</h1>
            <p class="text-gray-600">Informasi lengkap transaksi penjualan</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('kepala.laporan-penjualan') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Informasi Transaksi -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Informasi Transaksi</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600">No. Transaksi</p>
                <p class="text-lg font-semibold text-gray-800">{{ $transaksi->no_transaksi }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Tanggal Transaksi</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y H:i') }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Kasir</p>
                <p class="text-lg font-semibold text-gray-800">{{ $transaksi->kasir->nama ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    {{ $transaksi->status }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Pembayaran -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Informasi Pembayaran</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600">Metode Pembayaran</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($transaksi->metode_pembayaran === 'Tunai') bg-green-100 text-green-800
                    @elseif($transaksi->metode_pembayaran === 'Debit') bg-blue-100 text-blue-800
                    @elseif($transaksi->metode_pembayaran === 'QRIS') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $transaksi->metode_pembayaran }}
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Subtotal</p>
                <p class="text-lg font-semibold text-gray-800">
                    Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Diskon</p>
                <p class="text-lg font-semibold text-red-600">
                    - Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Total Bayar</p>
                <p class="text-2xl font-bold text-green-600">
                    Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Detail Item Transaksi -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Detail Item Transaksi</h2>
        <p class="text-sm text-gray-600 mt-1">Total {{ $transaksi->items->count() }} item</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transaksi->items as $index => $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $item->nama_produk }}</div>
                        @if($item->produk_id)
                        <div class="text-xs text-gray-500">ID: {{ $item->produk_id }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $item->qty }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900" colspan="2">Total</td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                        {{ $transaksi->items->sum('qty') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600">
                        Rp {{ number_format($transaksi->items->sum('subtotal'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Summary -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-blue-50 rounded-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-cubes text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-blue-600">Total Item</p>
                <p class="text-2xl font-bold text-blue-800">{{ $transaksi->total_item }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-green-50 rounded-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-green-600">Total Transaksi</p>
                <p class="text-2xl font-bold text-green-800">
                    Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-purple-50 rounded-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-purple-600">Waktu Transaksi</p>
                <p class="text-lg font-bold text-purple-800">
                    {{ \Carbon\Carbon::parse($transaksi->created_at)->format('H:i:s') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection