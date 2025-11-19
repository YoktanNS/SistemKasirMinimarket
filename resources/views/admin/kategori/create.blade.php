@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page_title', 'Tambah Kategori Baru')

@section('content')
<div class="bg-white shadow-md rounded-lg p-8 max-w-2xl mx-auto">
    <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Success/Error Messages -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong class="font-bold">Error!</strong>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nama Kategori *</label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                   class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400"
                   placeholder="Contoh: Makanan Ringan, Minuman, ATK"
                   required>
            @error('nama_kategori')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Deskripsi (Opsional)</label>
            <textarea name="deskripsi" rows="3"
                      class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400"
                      placeholder="Deskripsi singkat tentang kategori...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.kategori.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
                Simpan Kategori
            </button>
        </div>
    </form>
</div>
@endsection