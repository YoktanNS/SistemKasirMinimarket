@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page_title', 'Edit Data Supplier')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md max-w-3xl mx-auto">
    <form action="{{ route('admin.supplier.update', $supplier->supplier_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Kode Supplier</label>
            <input type="text" name="kode_supplier" value="{{ $supplier->kode_supplier }}" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nama Supplier</label>
            <input type="text" name="nama_supplier" value="{{ $supplier->nama_supplier }}" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">No. Telepon</label>
                <input type="text" name="no_telepon" value="{{ $supplier->no_telepon }}" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ $supplier->email }}" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">{{ $supplier->alamat }}</textarea>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Kontak Person</label>
            <input type="text" name="kontak_person" value="{{ $supplier->kontak_person }}" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="Aktif" {{ $supplier->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ $supplier->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="flex justify-end gap-3 pt-6">
            <a href="{{ route('admin.supplier.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">Update</button>
        </div>
    </form>
</div>
@endsection
