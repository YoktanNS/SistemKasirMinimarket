@extends('layouts.app')

@section('title', 'Detail Supplier')
@section('page_title', 'Detail Supplier')

@section('content')
<div class="bg-white shadow-md rounded-2xl p-8 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">📋 Detail Informasi Supplier</h2>
        <a href="{{ route('admin.supplier.index') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">
           ← Kembali
        </a>
    </div>

    <!-- Informasi Supplier -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <h3 class="text-gray-600 text-sm font-semibold mb-1">Kode Supplier</h3>
            <p class="text-lg font-medium text-gray-900">{{ $supplier->kode_supplier }}</p>
        </div>

        <div>
            <h3 class="text-gray-600 text-sm font-semibold mb-1">Nama Supplier</h3>
            <p class="text-lg font-medium text-gray-900">{{ $supplier->nama_supplier }}</p>
        </div>

        <div>
            <h3 class="text-gray-600 text-sm font-semibold mb-1">Kontak Person</h3>
            <p class="text-lg font-medium text-gray-900">{{ $supplier->kontak_person ?? '-' }}</p>
        </div>

        <div>
            <h3 class="text-gray-600 text-sm font-semibold mb-1">No. Telepon</h3>
            <p class="text-lg font-medium text-gray-900">{{ $supplier->no_telepon ?? '-' }}</p>
        </div>

        <div>
            <h3 class="text-gray-600 text-sm font-semibold mb-1">Email</h3>
            <p class="text-lg font-medium text-gray-900">{{ $supplier->email ?? '-' }}</p>
        </div>

        <div>
            <h3 class="text-gray-600 text-sm font-semibold mb-1">Status</h3>
            <span class="px-3 py-1 rounded-full text-sm font-semibold
                {{ $supplier->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $supplier->status }}
            </span>
        </div>
    </div>

    <!-- Alamat -->
    <div class="mb-8">
        <h3 class="text-gray-600 text-sm font-semibold mb-1">Alamat Lengkap</h3>
        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg border min-h-[80px]">
            {{ $supplier->alamat ?? 'Alamat belum diisi' }}
        </p>
    </div>

    <!-- Informasi Tambahan -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <span class="text-blue-600 text-lg">💡</span>
            <div>
                <h4 class="font-medium text-blue-800">Informasi Supplier</h4>
                <p class="text-blue-700 text-sm mt-1">
                    • Supplier ini dapat digunakan untuk pembelian produk<br>
                    • Pastikan informasi kontak selalu diperbarui<br>
                    • Status aktif/nonaktif dapat diubah sesuai kebutuhan
                </p>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
        <a href="{{ route('admin.supplier.edit', $supplier->supplier_id) }}"
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg shadow transition font-medium">
            ✏️ Edit Supplier
        </a>
        <form action="{{ route('admin.supplier.destroy', $supplier->supplier_id) }}" method="POST" 
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow transition font-medium">
                🗑️ Hapus Supplier
            </button>
        </form>
    </div>
</div>
@endsection