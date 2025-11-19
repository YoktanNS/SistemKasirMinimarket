@extends('layouts.app')

@section('title', 'Upload Dokumen - Arsip')
@section('page_title', '📤 Upload Dokumen Baru')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white shadow-lg rounded-2xl p-6 max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-wide">📤 Upload Dokumen Baru</h2>
            <p class="text-gray-500 text-sm mt-1">Tambah dokumen baru ke arsip</p>
        </div>
        <a href="{{ route('admin.arsip.index') }}?tab={{ $currentTab ?? 'all' }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow-md transition">
            ← Kembali
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.arsip.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="redirect_tab" value="{{ $currentTab ?? 'all' }}">
        
        <div class="bg-white rounded-xl shadow-lg p-6 space-y-6">
            <!-- Upload File Dokumen -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📎 Upload File Dokumen <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="file_dokumen" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max. 10MB)</p>
                        </div>
                        <input id="file_dokumen" name="file_dokumen" type="file" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required />
                    </label>
                </div>
                <div id="file-name" class="text-sm text-green-600 mt-2"></div>
                @error('file_dokumen')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Dokumen -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📄 Jenis Dokumen <span class="text-red-500">*</span>
                </label>
                <select name="jenis_laporan" class="w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                    <option value="">Pilih Jenis Dokumen</option>
                    <option value="Laporan Harian">Laporan Harian</option>
                    <option value="Laporan Bulanan">Laporan Bulanan</option>
                    <option value="Laporan Produk Terlaris">Laporan Produk Terlaris</option>
                    <option value="Laporan Profit Margin">Laporan Profit Margin</option>
                    <option value="Dokumen Lainnya">Dokumen Lainnya</option>
                </select>
                @error('jenis_laporan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Periode -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📅 Periode Awal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="periode_awal" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                           value="{{ old('periode_awal') }}" required>
                    @error('periode_awal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📅 Periode Akhir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="periode_akhir" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                           value="{{ old('periode_akhir') }}" required>
                    @error('periode_akhir')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Deskripsi / Ringkasan
                </label>
                <textarea name="ringkasan" rows="4" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                          placeholder="Ringkasan singkat tentang dokumen ini...">{{ old('ringkasan') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Opsional: Tambahkan deskripsi singkat tentang dokumen</p>
                @error('ringkasan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.arsip.index') }}?tab={{ $currentTab ?? 'all' }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg transition font-medium">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow transition font-medium flex items-center gap-2">
                💾 Upload & Simpan Dokumen
            </button>
        </div>
    </form>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
        <div class="flex items-start gap-3">
            <span class="text-blue-600 text-lg">💡</span>
            <div>
                <h4 class="font-medium text-blue-800">Tips Upload Dokumen</h4>
                <p class="text-blue-700 text-sm mt-1">
                    • Pastikan file dalam format PDF, DOC, DOCX, XLS, XLSX, JPG, PNG<br>
                    • Maksimal ukuran file: 10MB<br>
                    • Pastikan periode sudah sesuai dengan dokumen<br>
                    • Pilih jenis dokumen yang tepat
                </p>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk menampilkan nama file -->
<script>
document.getElementById('file_dokumen').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    const fileDisplay = document.getElementById('file-name');
    
    if (fileName) {
        fileDisplay.textContent = 'File terpilih: ' + fileName;
    } else {
        fileDisplay.textContent = '';
    }
});
</script>
@endsection