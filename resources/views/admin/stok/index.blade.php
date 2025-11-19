@extends('layouts.app')

@section('title', 'Manajemen Stok Barang')
@section('page_title', '📊 Manajemen Stok Barang')

@section('content')
<div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
    <!-- Header dengan Statistik -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-wide">📦 Manajemen Stok</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola stok masuk dan keluar dengan mudah</p>
        </div>
        
        <div class="flex gap-3">
            <!-- TOMBOL BUKA MODAL -->
            <a href="#stokModal" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg shadow transition flex items-center gap-2">
               <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Tabel Stok -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-700 border-b">Tanggal</th>
                    <th class="p-4 text-left font-semibold text-gray-700 border-b">Produk</th>
                    <th class="p-4 text-center font-semibold text-gray-700 border-b">Jenis</th>
                    <th class="p-4 text-center font-semibold text-gray-700 border-b">Jumlah</th>
                    <th class="p-4 text-center font-semibold text-gray-700 border-b">Supplier</th>
                    <th class="p-4 text-center font-semibold text-gray-700 border-b">Stok Sebelum</th>
                    <th class="p-4 text-center font-semibold text-gray-700 border-b">Stok Sesudah</th>
                    <th class="p-4 text-left font-semibold text-gray-700 border-b">Keterangan</th>
                    <th class="p-4 text-center font-semibold text-gray-700 border-b">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($stok as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- Tanggal -->
                        <td class="p-4 text-gray-600">
                            <div class="font-medium">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('H:i') }}</div>
                        </td>
                        
                        <!-- Produk -->
                        <td class="p-4">
                            <div class="font-semibold text-gray-800">{{ $item->produk->nama_produk ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->produk->kode_produk ?? '' }}</div>
                        </td>
                        
                        <!-- Jenis Transaksi -->
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                {{ $item->jenis_transaksi == 'Masuk' 
                                    ? 'bg-green-100 text-green-700 border border-green-200' 
                                    : 'bg-red-100 text-red-700 border border-red-200' }}">
                                {{ $item->jenis_transaksi }}
                            </span>
                        </td>
                        
                        <!-- Jumlah -->
                        <td class="p-4 text-center">
                            <span class="font-semibold {{ $item->jenis_transaksi == 'Masuk' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $item->jumlah }} {{ $item->produk->satuan ?? '' }}
                            </span>
                        </td>
                        
                        <!-- Supplier -->
                        <td class="p-4 text-center text-gray-600">
                            {{ $item->supplier->nama_supplier ?? '-' }}
                        </td>
                        
                        <!-- Stok Sebelum -->
                        <td class="p-4 text-center text-gray-500">
                            {{ $item->stok_sebelum }}
                        </td>
                        
                        <!-- Stok Sesudah -->
                        <td class="p-4 text-center">
                            <span class="font-bold text-blue-600">{{ $item->stok_sesudah }}</span>
                        </td>
                        
                        <!-- Keterangan -->
                        <td class="p-4 text-gray-600 max-w-xs">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                        
                        <!-- User -->
                        <td class="p-4 text-center text-gray-500 text-xs">
                            {{ $item->user->nama_lengkap ?? 'System' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                                <p class="text-lg">Belum ada aktivitas stok</p>
                                <p class="text-sm mt-1">Mulai dengan menambahkan transaksi stok pertama</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $stok->links() }}
    </div>
</div>

<!-- MODAL TAMBAH TRANSAKSI STOK -->
<div id="stokModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">➕ Tambah Transaksi Stok</h3>
            <a href="#" class="text-gray-500 hover:text-gray-700 text-xl">
                <i class="fas fa-times"></i>
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.stok.store') }}" id="stokForm">
            @csrf
            <div class="space-y-4">
                <!-- Produk -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-cube mr-2"></i>Produk *
                    </label>
                    <select name="produk_id" id="produk_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produk as $p)
                            <option value="{{ $p->produk_id }}" 
                                    data-stok="{{ $p->stok_tersedia }}">
                                {{ $p->nama_produk }} (Stok: {{ $p->stok_tersedia }} {{ $p->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Jenis Transaksi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-exchange-alt mr-2"></i>Jenis Transaksi *
                    </label>
                    <select name="jenis_transaksi" id="jenis_transaksi" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="Masuk">🟢 Stok Masuk (Tambah)</option>
                        <option value="Keluar">🔴 Stok Keluar (Kurangi)</option>
                    </select>
                </div>

                <!-- Supplier (Hanya untuk Stok Masuk) -->
                <div id="supplier_field" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-truck mr-2"></i>Supplier *
                    </label>
                    <select name="supplier_id" id="supplier_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Jumlah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hashtag mr-2"></i>Jumlah *
                    </label>
                    <input type="number" name="jumlah" id="jumlah" min="1" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Masukkan jumlah">
                    <div id="stok_info" class="text-sm text-gray-500 mt-1"></div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2"></i>Keterangan
                    </label>
                    <textarea name="keterangan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                              placeholder="Contoh: Restock dari supplier, Penjualan retail, Stok opname, dll"></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="#" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg font-medium transition">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow font-medium transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
    
    <!-- Backdrop -->
    <a href="#" class="modal-close"></a>
</div>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: flex-start;
    justify-content: center;
    padding: 20px;
    overflow-y: auto;
}

.modal:target {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    width: 100%;
    max-width: 500px;
    margin: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1001;
}

.modal-close {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
}

/* Custom Scrollbar */
.modal-content::-webkit-scrollbar {
    width: 6px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisTransaksi = document.getElementById('jenis_transaksi');
    const supplierField = document.getElementById('supplier_field');
    const produkSelect = document.getElementById('produk_id');
    const jumlahInput = document.getElementById('jumlah');
    const stokInfo = document.getElementById('stok_info');

    // Toggle field supplier berdasarkan jenis transaksi
    function toggleFields() {
        const isStokMasuk = jenisTransaksi.value === 'Masuk';
        
        if (isStokMasuk) {
            supplierField.style.display = 'block';
            document.getElementById('supplier_id').required = true;
        } else {
            supplierField.style.display = 'none';
            document.getElementById('supplier_id').required = false;
            document.getElementById('supplier_id').value = '';
        }
        updateStokInfo();
    }

    // Update info stok saat produk atau jenis transaksi berubah
    function updateStokInfo() {
        const selectedOption = produkSelect.options[produkSelect.selectedIndex];
        const stokTersedia = selectedOption.getAttribute('data-stok') || 0;
        const isStokKeluar = jenisTransaksi.value === 'Keluar';
        
        if (produkSelect.value && isStokKeluar) {
            stokInfo.innerHTML = `Stok tersedia: <span class="font-semibold ${stokTersedia < 10 ? 'text-red-600' : 'text-green-600'}">${stokTersedia}</span>`;
            jumlahInput.max = stokTersedia;
        } else if (produkSelect.value) {
            stokInfo.innerHTML = `Stok tersedia: <span class="font-semibold text-blue-600">${stokTersedia}</span>`;
            jumlahInput.removeAttribute('max');
        } else {
            stokInfo.innerHTML = '';
            jumlahInput.removeAttribute('max');
        }
    }

    // Validasi form
    document.getElementById('stokForm').addEventListener('submit', function(e) {
        const isStokMasuk = jenisTransaksi.value === 'Masuk';
        const isStokKeluar = jenisTransaksi.value === 'Keluar';
        const selectedOption = produkSelect.options[produkSelect.selectedIndex];
        const stokTersedia = parseInt(selectedOption.getAttribute('data-stok')) || 0;
        const jumlah = parseInt(jumlahInput.value) || 0;

        if (isStokKeluar && jumlah > stokTersedia) {
            e.preventDefault();
            alert(`Stok tidak mencukupi! Stok tersedia: ${stokTersedia}`);
            return false;
        }

        if (isStokMasuk && !document.getElementById('supplier_id').value) {
            e.preventDefault();
            alert('Harap pilih supplier untuk stok masuk!');
            return false;
        }
    });

    // Event listeners
    jenisTransaksi.addEventListener('change', toggleFields);
    produkSelect.addEventListener('change', updateStokInfo);
    jumlahInput.addEventListener('input', updateStokInfo);

    // Initialize
    toggleFields();
});
</script>
@endsection