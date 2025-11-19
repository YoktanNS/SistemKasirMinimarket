@extends('layouts.app')

@section('title', 'Kelola Produk')
@section('page_title', '📦 Manajemen Data Produk')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Daftar Produk</h2>
    <a href="{{ route('admin.produk.create') }}"
        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
        + Tambah Produk
    </a>
</div>

<!-- Search Bar -->
<form method="GET" action="{{ route('admin.produk.index') }}" class="flex items-center gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
        placeholder="🔍 Cari produk berdasarkan nama, kategori, atau supplier..."
        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
    <button type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition">
        Cari
    </button>
</form>

<!-- Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
    <table class="w-full text-sm text-gray-700">
        <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Kode Produk</th>
                <th class="px-4 py-3 text-left">Nama Produk</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-center">Harga</th>
                <th class="px-4 py-3 text-center">Stok</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($produk as $item)
                <tr class="hover:bg-blue-50 transition">
                    <td class="px-4 py-3">
                        <span class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded border cursor-help" 
                              title="{{ $item->barcode }}">
                            {{ \Illuminate\Support\Str::limit($item->barcode, 12, '...') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $item->nama_produk }}</td>
                    <td class="px-4 py-3">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-gray-700">
                        <span class="font-semibold">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="{{ $item->stok_tersedia <= $item->stok_minimum ? 'bg-red-100 text-red-700 px-3 py-1 rounded-full font-semibold' : 'bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold' }}">
                            {{ $item->stok_tersedia }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center space-x-2">
                        <a href="{{ route('admin.produk.edit', $item->produk_id) }}"
                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md font-medium shadow transition">
                            Edit
                        </a>
                        <button onclick="confirmDelete('{{ route('admin.produk.destroy', $item->produk_id) }}')"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md font-medium shadow transition">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500 font-medium">Belum ada produk yang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $produk->links() }}
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal"
    class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-96 text-center shadow-2xl border-t-4 border-red-500">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-5">Apakah Anda yakin ingin menghapus produk ini?</p>
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