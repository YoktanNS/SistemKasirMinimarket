@extends('layouts.app')

@section('title', 'Data Supplier')
@section('page_title', 'Manajemen Supplier')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
        🚚 Data Supplier
    </h2>
    <a href="{{ route('admin.supplier.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition-all">
       + Tambah Supplier
    </a>
</div>

<!-- Search bar -->
<form method="GET" action="{{ route('admin.supplier.index') }}" class="mb-6 flex items-center gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
        placeholder="Cari nama supplier atau email..."
        class="border border-gray-300 rounded-lg px-4 py-2 w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
        🔍 Cari
    </button>
</form>

<!-- Table -->
<div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
    <table class="w-full text-sm text-gray-800">
        <thead class="bg-blue-600 text-white uppercase text-sm">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama Supplier</th>
                <th class="px-4 py-3 text-left">Kontak</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Total PO</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="px-4 py-3 font-medium">{{ $supplier->kode_supplier }}</td>
                    <td class="px-4 py-3">{{ $supplier->nama_supplier }}</td>
                    <td class="px-4 py-3">{{ $supplier->no_telepon }}</td>
                    <td class="px-4 py-3">{{ $supplier->email ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold 
                            {{ $supplier->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ $supplier->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-blue-700 font-semibold">
                        {{ $supplier->purchase_orders_count ?? 0 }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('admin.supplier.show', $supplier->supplier_id) }}"
                               class="text-blue-600 hover:underline font-medium">Detail</a>
                            <a href="{{ route('admin.supplier.edit', $supplier->supplier_id) }}"
                               class="text-yellow-600 hover:underline font-medium">Edit</a>
                            <button onclick="confirmDelete('{{ route('admin.supplier.destroy', $supplier->supplier_id) }}')" 
                               class="text-red-600 hover:underline font-medium">Hapus</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-gray-500">Belum ada data supplier.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $suppliers->links() }}
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 text-center shadow-xl">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-5">Apakah Anda yakin ingin menghapus supplier ini?</p>
        <div class="flex justify-center gap-4">
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">
                    Ya, Hapus
                </button>
            </form>
            <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg">
                Batal
            </button>
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
