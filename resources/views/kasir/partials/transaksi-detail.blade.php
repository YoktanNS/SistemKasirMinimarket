<div class="space-y-4">
    <!-- Header Info -->
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">No. Transaksi</label>
            <p class="font-semibold text-lg text-blue-600">{{ $transaksi->no_transaksi }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
            <p class="font-semibold">{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Kasir</label>
            <p class="font-semibold">{{ $transaksi->kasir->name ?? 'System' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Metode Bayar</label>
            <span class="px-2 py-1 rounded-full text-xs font-medium 
                {{ $transaksi->metode_pembayaran == 'Tunai' ? 'bg-green-100 text-green-800' : 
                   ($transaksi->metode_pembayaran == 'QRIS' ? 'bg-purple-100 text-purple-800' : 
                   'bg-blue-100 text-blue-800') }}">
                {{ $transaksi->metode_pembayaran }}
            </span>
        </div>
    </div>

    <!-- Items Table -->
    <div class="border rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Produk</th>
                    <th class="px-4 py-2 text-right">Qty</th>
                    <th class="px-4 py-2 text-right">Harga</th>
                    <th class="px-4 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($transaksi->items as $item)
                <tr>
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ $item->nama_produk }}</div>
                        <div class="text-xs text-gray-500">{{ $item->barcode }}</div>
                    </td>
                    <td class="px-4 py-2 text-right">{{ $item->qty }}</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-2 gap-2 text-sm">
            <div class="text-right">Subtotal:</div>
            <div class="text-right font-semibold">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</div>
            
            @if($transaksi->diskon > 0)
            <div class="text-right">Diskon:</div>
            <div class="text-right font-semibold text-red-600">- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</div>
            @endif
            
            <div class="text-right text-lg font-bold border-t pt-2">Total:</div>
            <div class="text-right text-lg font-bold text-green-600 border-t pt-2">
                Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}
            </div>
            
            @if($transaksi->metode_pembayaran === 'Tunai')
            <div class="text-right">Tunai:</div>
            <div class="text-right font-semibold">Rp {{ number_format($transaksi->jumlah_uang, 0, ',', '.') }}</div>
            
            <div class="text-right">Kembali:</div>
            <div class="text-right font-semibold text-blue-600">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</div>
            @endif
        </div>
    </div>

    <!-- Status -->
    <div class="text-center">
        <span class="px-3 py-1 rounded-full text-sm font-medium 
            {{ $transaksi->status == 'Selesai' ? 'bg-green-100 text-green-800' : 
               ($transaksi->status == 'Dibatalkan' ? 'bg-red-100 text-red-800' : 
               'bg-yellow-100 text-yellow-800') }}">
            Status: {{ $transaksi->status }}
        </span>
    </div>
</div>