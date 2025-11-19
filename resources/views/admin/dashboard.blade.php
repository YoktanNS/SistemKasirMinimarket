@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Admin')

@section('content')
<div class="space-y-8 animate-fade-in">
  <!-- Kartu Statistik -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @php
      $stats = [
        ['label' => 'Total Produk', 'value' => $totalProduk, 'icon' => '📦', 'color' => 'from-blue-500 to-blue-600'],
        ['label' => 'Total Supplier', 'value' => $totalSupplier, 'icon' => '🚚', 'color' => 'from-green-500 to-green-600'],
        ['label' => 'Total Transaksi', 'value' => $totalTransaksi, 'icon' => '💳', 'color' => 'from-yellow-400 to-yellow-500'],
        ['label' => 'Stok Menipis', 'value' => count($stokMenipis), 'icon' => '⚠️', 'color' => 'from-red-500 to-red-600'],
      ];
    @endphp

    @foreach($stats as $stat)
      <div class="bg-gradient-to-r {{ $stat['color'] }} text-white p-6 rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-sm font-medium opacity-80">{{ $stat['label'] }}</h3>
            <p class="text-4xl font-bold mt-1">{{ $stat['value'] }}</p>
          </div>
          <div class="text-5xl opacity-30">{{ $stat['icon'] }}</div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Tabel Produk Stok Menipis -->
  @if(count($stokMenipis) > 0)
  <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-700">⚠️ Produk dengan Stok Menipis</h3>
      <span class="text-sm text-gray-500">{{ now()->format('d M Y, H:i') }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-200 rounded-lg">
        <thead class="bg-blue-50 text-blue-700">
          <tr>
            <th class="p-3 border text-left font-semibold">Nama Produk</th>
            <th class="p-3 border text-center font-semibold">Stok Tersedia</th>
            <th class="p-3 border text-center font-semibold">Stok Minimum</th>
            <th class="p-3 border text-center font-semibold">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($stokMenipis as $produk)
          <tr class="hover:bg-gray-50 transition duration-200">
            <td class="p-3 border">{{ $produk->nama_produk }}</td>
            <td class="p-3 border text-center text-red-600 font-semibold">{{ $produk->stok_tersedia }}</td>
            <td class="p-3 border text-center">{{ $produk->stok_minimum }}</td>
            <td class="p-3 border text-center">
              <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">Menipis</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @else
  <div class="text-center py-12 bg-white rounded-2xl shadow-md">
    <h3 class="text-xl font-semibold mb-2 text-green-600">🎉 Semua stok aman!</h3>
    <p class="text-gray-500">Tidak ada produk yang mencapai batas minimum.</p>
  </div>
  @endif
</div>

<!-- Animasi -->
<style>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.5s ease-out both;
}
</style>
@endsection
