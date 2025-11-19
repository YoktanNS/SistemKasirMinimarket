@extends('layouts.app')

@section('title', 'Kelola Kategori')
@section('page_title', '🗂️ Manajemen Kategori Produk')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Daftar Kategori</h2>
    <a href="{{ route('admin.kategori.create') }}"
        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
        + Tambah Kategori
    </a>
</div>

<!-- Search Bar -->
<form method="GET" action="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
        placeholder="🔍 Cari kategori..."
        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-500">
    <button type="submit"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition">
        Cari
    </button>
</form>

<!-- Success/Error Messages -->
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

<!-- Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
    <table class="w-full text-sm text-gray-700">
        <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Nama Kategori</th>
                <th class="px-4 py-3 text-left">Deskripsi</th>
                <th class="px-4 py-3 text-center">Jumlah Produk</th>
                <th class="px-4 py-3 text-center">Dibuat</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($kategori as $item)
                <tr class="hover:bg-green-50 transition">
                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $item->nama_kategori }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $item->deskripsi ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold">
                            {{ $item->produk->count() }} produk
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500">
                        {{ $item->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 text-center space-x-2">
                        <a href="{{ route('admin.kategori.edit', $item->kategori_id) }}"
                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md font-medium shadow transition">
                            Edit
                        </a>
                        <button onclick="confirmDelete('{{ route('admin.kategori.destroy', $item->kategori_id) }}')"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md font-medium shadow transition">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500 font-medium">
                        @if(request('search'))
                            Tidak ada kategori yang sesuai dengan pencarian.
                        @else
                            Belum ada kategori yang terdaftar.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $kategori->links() }}
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal"
    class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-96 text-center shadow-2xl border-t-4 border-red-500">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-5">Apakah Anda yakin ingin menghapus kategori ini?</p>
        <div class="flex justify-center gap-4">
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow transition">Ya, Hapus</button>
            </form>
            <button onclick="closeModal()"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">Batal</button>
        </div>
    </div>
</div>

<script>
    function confirmDelete(actionUrl) {
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection