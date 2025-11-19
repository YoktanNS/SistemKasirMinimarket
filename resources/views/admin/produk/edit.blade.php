@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page_title', 'Edit Data Produk')

@section('content')
<div class="bg-white shadow-md rounded-lg p-8 max-w-3xl mx-auto">
    <form action="{{ route('admin.produk.update', $produk->produk_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nama Produk</label>
            <input type="text" name="nama_produk" value="{{ $produk->nama_produk }}" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" required>
                    @foreach($kategoris as $item)
                        <option value="{{ $item->kategori_id }}" {{ $produk->kategori_id == $item->kategori_id ? 'selected' : '' }}>
                            {{ $item->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" required>
                    @foreach($suppliers as $item)
                        <option value="{{ $item->supplier_id }}" {{ $produk->supplier_id == $item->supplier_id ? 'selected' : '' }}>
                            {{ $item->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Harga Beli</label>
                <input type="number" name="harga_beli" value="{{ $produk->harga_beli }}" step="0.01" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Harga Jual</label>
                <input type="number" name="harga_jual" value="{{ $produk->harga_jual }}" step="0.01" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Stok Minimum</label>
                <input type="number" name="stok_minimum" value="{{ $produk->stok_minimum }}" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Satuan</label>
                <input type="text" name="satuan" value="{{ $produk->satuan }}" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" required>
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
                        <div id="imagePreview">
                            @if($produk->gambar_produk)
                                <img src="{{ asset('storage/' . $produk->gambar_produk) }}" 
                                     class="max-h-40 mx-auto rounded-lg shadow" 
                                     alt="Gambar Produk">
                                <p class="text-xs text-gray-500 mt-2">Gambar saat ini</p>
                            @else
                                <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                <p class="text-sm text-gray-500">Belum ada gambar</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Upload Area -->
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Upload Gambar Baru</label>
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
                    @if($produk->gambar_produk)
                        <p class="text-xs text-gray-500 text-center mt-1">
                            Kosongkan jika tidak ingin mengubah gambar
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">{{ $produk->deskripsi }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.produk.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">Batal</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">Simpan Perubahan</button>
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