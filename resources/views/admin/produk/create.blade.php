@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page_title', 'Tambah Produk Baru')

@section('content')
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

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
    {{ session('error') }}
</div>
@endif

<div class="bg-white shadow-md rounded-lg p-8 max-w-3xl mx-auto">
    <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nama Produk</label>
            <input type="text" name="nama_produk" value="{{ old('nama_produk') }}"
                   class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
                   required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id" id="kategoriSelect"
                        class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->kategori_id }}" {{ old('kategori_id') == $k->kategori_id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Supplier</label>
                <select name="supplier_id"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
                        required>
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $item)
                        <option value="{{ $item->supplier_id }}" {{ old('supplier_id') == $item->supplier_id ? 'selected' : '' }}>
                            {{ $item->nama_supplier }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Harga Beli</label>
                <input type="number" name="harga_beli" step="0.01" value="{{ old('harga_beli') }}"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
                       required>
                @error('harga_beli')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Harga Jual</label>
                <input type="number" name="harga_jual" step="0.01" value="{{ old('harga_jual') }}"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
                       required>
                @error('harga_jual')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Stok Minimum</label>
                <input type="number" name="stok_minimum" value="{{ old('stok_minimum', 5) }}"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
                       required>
                @error('stok_minimum')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Satuan</label>
                <input type="text" name="satuan" value="{{ old('satuan') }}"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
                       placeholder="pcs, pack, botol, dll" required>
                @error('satuan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- PERBAIKAN: Form Input Gambar dengan Preview -->
        <div>
            <label class="block font-semibold text-gray-700 mb-3">Gambar Produk</label>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Preview Area -->
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Preview</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50 h-48 flex items-center justify-center">
                        <div id="imagePreview" class="text-center">
                            <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                            <p class="text-sm text-gray-500">Gambar akan muncul di sini</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Area -->
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Upload Gambar</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition cursor-pointer bg-white"
                         onclick="document.getElementById('gambar_produk').click()">
                        <input type="file" 
                               name="gambar_produk" 
                               id="gambar_produk" 
                               class="hidden" 
                               accept="image/*"
                               onchange="previewImage(this)">
                        
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">
                                <span class="text-blue-600 font-medium">Klik untuk upload</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                PNG, JPG, JPEG (Max. 2MB)
                            </p>
                        </div>
                    </div>
                    <p id="fileName" class="text-sm text-gray-600 mt-2 text-center"></p>
                    @error('gambar_produk')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Deskripsi Produk</label>
            <textarea name="deskripsi" rows="4"
                      class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.produk.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const fileName = document.getElementById('fileName');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="max-h-40 mx-auto rounded-lg shadow" alt="Preview">`;
        }
        
        reader.readAsDataURL(input.files[0]);
        fileName.textContent = 'File: ' + input.files[0].name;
    }
}
</script>

<style>
#imagePreview img {
    max-width: 100%;
    max-height: 160px;
    object-fit: cover;
}
</style>
@endsection