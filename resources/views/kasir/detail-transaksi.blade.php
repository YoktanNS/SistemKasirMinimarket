<!-- Detail Transaksi Content -->
<div class="space-y-6">
    <!-- Header Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="border rounded-lg p-4">
            <h4 class="font-semibold text-gray-700 mb-3">Informasi Transaksi</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">No. Transaksi:</span>
                    <span class="font-semibold">{{ $transaksi->no_transaksi }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal:</span>
                    <span>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kasir:</span>
                    <span>{{ $transaksi->kasir->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                        {{ $transaksi->status == 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $transaksi->status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="border rounded-lg p-4">
            <h4 class="font-semibold text-gray-700 mb-3">Informasi Pembayaran</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode Bayar:</span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                        {{ $transaksi->metode_pembayaran == 'Tunai' ? 'bg-green-100 text-green-800' : 
                           ($transaksi->metode_pembayaran == 'QRIS' ? 'bg-purple-100 text-purple-800' : 
                           'bg-blue-100 text-blue-800') }}">
                        {{ $transaksi->metode_pembayaran }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Diskon:</span>
                    <span>Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Bayar:</span>
                    <span class="font-semibold text-green-600">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Jumlah Uang:</span>
                    <span>Rp {{ number_format($transaksi->jumlah_uang, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kembalian:</span>
                    <span class="font-semibold text-blue-600">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="border rounded-lg overflow-hidden">
        <h4 class="font-semibold text-gray-700 p-4 bg-gray-50">Daftar Item</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Produk</th>
                        <th class="px-4 py-2 text-left font-semibold">Barcode</th>
                        <th class="px-4 py-2 text-right font-semibold">Harga</th>
                        <th class="px-4 py-2 text-right font-semibold">Qty</th>
                        <th class="px-4 py-2 text-right font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transaksi->items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->nama_produk }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->barcode }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->qty }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-semibold">Total:</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ $transaksi->total_item }} items</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">
                            Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3 pt-4">
        <a href="{{ route('kasir.cetak-struk', $transaksi->transaksi_id) }}" 
           target="_blank"
           class="btn-kasir-primary">
            <i class="fas fa-print mr-2"></i>Cetak Ulang Struk
        </a>
        
        @if($transaksi->status == 'Selesai' && $transaksi->canBeCancelled())
        <button onclick="batalkanTransaksi('{{ $transaksi->transaksi_id }}')" 
                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
            <i class="fas fa-times mr-2"></i>Batalkan Transaksi
        </button>
        @endif
    </div>
</div>