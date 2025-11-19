@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-history mr-2"></i>Riwayat Transaksi
        </h1>
        <div class="flex space-x-3">
            <!-- Tombol Hapus Multiple -->
            <button id="delete-multiple-btn" 
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition opacity-50 cursor-not-allowed"
                    disabled>
                <i class="fas fa-trash mr-2"></i>Hapus Terpilih
            </button>
            <a href="{{ route('kasir.index') }}" class="btn-kasir-primary">
                <i class="fas fa-plus mr-2"></i>Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('kasir.riwayat') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">No. Transaksi</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari no. transaksi..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Bayar</label>
                <select name="metode_pembayaran" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="Tunai" {{ request('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="Debit" {{ request('metode_pembayaran') == 'Debit' ? 'selected' : '' }}>Debit</option>
                    <option value="QRIS" {{ request('metode_pembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="Transfer" {{ request('metode_pembayaran') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-kasir-secondary flex-1">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('kasir.riwayat') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="bg-blue-100 p-3 rounded-full inline-block mb-3">
                <i class="fas fa-receipt text-blue-500 text-xl"></i>
            </div>
            <p class="text-sm text-gray-600">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-900">{{ $transaksi->total() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="bg-green-100 p-3 rounded-full inline-block mb-3">
                <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
            </div>
            <p class="text-sm text-gray-600">Total Penjualan</p>
            <p class="text-2xl font-bold text-gray-900">
                Rp {{ number_format($transaksi->sum('total_bayar'), 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="bg-purple-100 p-3 rounded-full inline-block mb-3">
                <i class="fas fa-cube text-purple-500 text-xl"></i>
            </div>
            <p class="text-sm text-gray-600">Total Item Terjual</p>
            <p class="text-2xl font-bold text-gray-900">{{ $transaksi->sum('total_item') }}</p>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold w-12">
                            <input type="checkbox" id="select-all-checkbox" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">No. Transaksi</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Kasir</th>
                        <th class="px-4 py-3 text-right font-semibold">Items</th>
                        <th class="px-4 py-3 text-right font-semibold">Total Bayar</th>
                        <th class="px-4 py-3 text-left font-semibold">Metode</th>
                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transaksi as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="transaksi-checkbox rounded" value="{{ $item->transaksi_id }}">
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-blue-600">{{ $item->no_transaksi }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->tanggal_transaksi->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $item->tanggal_transaksi->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->kasir->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $item->total_item }} items
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">
                            Rp {{ number_format($item->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $item->metode_pembayaran == 'Tunai' ? 'bg-green-100 text-green-800' : 
                                   ($item->metode_pembayaran == 'QRIS' ? 'bg-purple-100 text-purple-800' : 
                                   'bg-blue-100 text-blue-800') }}">
                                {{ $item->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $item->status == 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- Detail Button -->
                                <button onclick="showDetail('{{ $item->transaksi_id }}')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg transition tooltip"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Cetak Struk -->
                                <a href="{{ route('kasir.cetak-struk', $item->transaksi_id) }}" 
                                   target="_blank"
                                   class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition tooltip"
                                   title="Cetak Struk">
                                    <i class="fas fa-print"></i>
                                </a>

                                <!-- Hapus Transaksi -->
                                <form method="POST" action="{{ route('kasir.riwayat.hapus', $item->transaksi_id) }}" 
                                      class="inline" onsubmit="return confirmHapusTransaksi(this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition tooltip"
                                            title="Hapus Transaksi">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-receipt text-4xl mb-3"></i>
                            <p class="text-lg">Belum ada transaksi</p>
                            <p class="text-sm mt-1">Mulai dengan membuat transaksi baru</p>
                            <a href="{{ route('kasir.index') }}" class="btn-kasir-primary mt-4 inline-block">
                                <i class="fas fa-plus mr-2"></i>Transaksi Baru
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transaksi->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">Detail Transaksi</h3>
            <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="modalContent">
            <!-- Content will be loaded via AJAX -->
        </div>
        
        <div class="flex justify-end mt-6">
            <button onclick="closeDetailModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Form untuk Hapus Multiple (Hidden) -->
<form id="deleteMultipleForm" method="POST" action="{{ route('kasir.riwayat.hapus-multiple') }}" class="hidden">
    @csrf
    @method('POST')
    <input type="hidden" name="transaksi_ids" id="transaksiIdsInput">
</form>

<script>
    // Show transaction detail
    async function showDetail(transaksiId) {
        try {
            const response = await fetch(`/kasir/riwayat/${transaksiId}`);
            const html = await response.text();
            
            document.getElementById('modalContent').innerHTML = html;
            document.getElementById('detailModal').classList.remove('hidden');
        } catch (error) {
            alert('Error memuat detail transaksi');
        }
    }

    // Close detail modal
    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // Confirm hapus single transaksi
    function confirmHapusTransaksi(form) {
        return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok produk akan dikembalikan. Tindakan ini tidak dapat dibatalkan.');
    }

    // Batalkan transaksi
    async function batalkanTransaksi(transaksiId) {
        if (!confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        try {
            const response = await fetch(`/kasir/riwayat/batalkan/${transaksiId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                alert('Transaksi berhasil dibatalkan');
                location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('Error membatalkan transaksi');
        }
    }

    // Multiple selection functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const transaksiCheckboxes = document.querySelectorAll('.transaksi-checkbox');
        const deleteMultipleBtn = document.getElementById('delete-multiple-btn');
        const deleteMultipleForm = document.getElementById('deleteMultipleForm');
        const transaksiIdsInput = document.getElementById('transaksiIdsInput');

        // Select All Checkbox
        selectAllCheckbox.addEventListener('change', function() {
            transaksiCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });

        // Individual checkbox change
        transaksiCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateDeleteButton();
                // Update select all checkbox state
                const allChecked = Array.from(transaksiCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });

        // Update delete button state
        function updateDeleteButton() {
            const checkedBoxes = Array.from(transaksiCheckboxes).filter(cb => cb.checked);
            
            if (checkedBoxes.length > 0) {
                deleteMultipleBtn.disabled = false;
                deleteMultipleBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                deleteMultipleBtn.classList.add('cursor-pointer');
            } else {
                deleteMultipleBtn.disabled = true;
                deleteMultipleBtn.classList.add('opacity-50', 'cursor-not-allowed');
                deleteMultipleBtn.classList.remove('cursor-pointer');
            }
        }

        // Delete multiple transactions
        deleteMultipleBtn.addEventListener('click', function() {
            const checkedBoxes = Array.from(transaksiCheckboxes).filter(cb => cb.checked);
            const transaksiIds = checkedBoxes.map(cb => cb.value);
            
            if (transaksiIds.length === 0) {
                alert('Pilih setidaknya satu transaksi untuk dihapus');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin menghapus ${transaksiIds.length} transaksi? Stok produk akan dikembalikan. Tindakan ini tidak dapat dibatalkan.`)) {
                transaksiIdsInput.value = JSON.stringify(transaksiIds);
                deleteMultipleForm.submit();
            }
        });

        // Tooltip initialization
        const tooltips = document.querySelectorAll('.tooltip');
        tooltips.forEach(tooltip => {
            tooltip.addEventListener('mouseenter', function(e) {
                const title = this.getAttribute('title');
                if (title) {
                    const tooltipEl = document.createElement('div');
                    tooltipEl.className = 'fixed bg-gray-800 text-white px-2 py-1 rounded text-xs z-50';
                    tooltipEl.textContent = title;
                    document.body.appendChild(tooltipEl);
                    
                    const rect = this.getBoundingClientRect();
                    tooltipEl.style.left = rect.left + 'px';
                    tooltipEl.style.top = (rect.top - 30) + 'px';
                    
                    this.setAttribute('data-tooltip', tooltipEl);
                    this.removeAttribute('title');
                }
            });
            
            tooltip.addEventListener('mouseleave', function() {
                const tooltipEl = this.getAttribute('data-tooltip');
                if (tooltipEl) {
                    document.body.removeChild(tooltipEl);
                    this.setAttribute('title', tooltipEl.textContent);
                }
            });
        });
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
        }
    });
</script>

<style>
    .tooltip {
        position: relative;
    }
    
    /* Pagination styling */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    
    .pagination li {
        margin: 0 2px;
    }
    
    .pagination li a,
    .pagination li span {
        display: block;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .pagination li a:hover {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    .pagination li.active span {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    .pagination li.disabled span {
        color: #9ca3af;
        cursor: not-allowed;
    }

    /* Checkbox styling */
    .transaksi-checkbox:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
</style>
@endsection