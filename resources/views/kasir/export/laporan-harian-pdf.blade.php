<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Harian Kasir</title>
    <style>
        /* Reset dan base styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        
        /* Layout */
        .container { max-width: 100%; padding: 20px; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #3b82f6; }
        .header h1 { color: #1e40af; font-size: 20px; margin-bottom: 5px; }
        .header .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 3px; }
        .header .info { color: #9ca3af; font-size: 11px; }
        
        /* Summary Cards */
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
        .summary-card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; text-align: center; background: #f8fafc; }
        .summary-value { font-size: 16px; font-weight: bold; color: #1f2937; margin: 3px 0; }
        .summary-label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
        
        /* Tables */
        .section { margin-bottom: 20px; }
        .section-header { background: #f8fafc; padding: 10px 12px; border: 1px solid #e5e7eb; border-bottom: none; border-radius: 4px 4px 0 0; }
        .section-title { font-size: 13px; font-weight: bold; color: #374151; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f3f4f6; text-align: left; font-weight: 600; font-size: 9px; text-transform: uppercase; color: #374151; padding: 8px 10px; border: 1px solid #e5e7eb; }
        td { padding: 8px 10px; border: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background: #fafafa; }
        
        /* Text alignment */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        
        /* Status badges */
        .badge { padding: 2px 6px; border-radius: 8px; font-size: 8px; font-weight: 600; display: inline-block; }
        .badge-tunai { background: #dcfce7; color: #166534; }
        .badge-debit { background: #dbeafe; color: #1e40af; }
        .badge-qris { background: #f3e8ff; color: #7e22ce; }
        .badge-transfer { background: #f1f5f9; color: #475569; }
        
        /* Footer */
        .footer { margin-top: 25px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 9px; }
        
        /* Kas Summary */
        .kas-summary { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .kas-item { display: flex; justify-content: between; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb; }
        .kas-item:last-child { border-bottom: none; margin-bottom: 0; }
        .kas-label { flex: 1; font-weight: 500; }
        .kas-value { font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>LAPORAN HARIAN KASIR</h1>
            <div class="subtitle">{{ $tanggalFormatted }}</div>
            <div class="info">Kasir: {{ $kasir->nama }} | Dicetak: {{ $tanggalCetak }}</div>
        </div>

        <!-- Summary Statistics -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
                <div class="summary-label">Total Transaksi</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                <div class="summary-label">Total Penjualan</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</div>
                <div class="summary-label">Rata-rata</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ number_format($totalItemTerjual, 0, ',', '.') }}</div>
                <div class="summary-label">Item Terjual</div>
            </div>
        </div>

        <!-- Ringkasan Kas -->
        @if($kasHarian)
        <div class="section">
            <div class="section-header">
                <div class="section-title">RINGKASAN KAS HARIAN</div>
            </div>
            <div class="kas-summary">
                <div class="kas-item">
                    <span class="kas-label">Saldo Awal</span>
                    <span class="kas-value text-right">Rp {{ number_format($kasHarian->saldo_awal, 0, ',', '.') }}</span>
                </div>
                <div class="kas-item">
                    <span class="kas-label">Penerimaan Tunai</span>
                    <span class="kas-value text-right">Rp {{ number_format($kasHarian->penerimaan_tunai, 0, ',', '.') }}</span>
                </div>
                <div class="kas-item">
                    <span class="kas-label">Penerimaan Non-Tunai</span>
                    <span class="kas-value text-right">Rp {{ number_format($kasHarian->penerimaan_non_tunai, 0, ',', '.') }}</span>
                </div>
                <div class="kas-item">
                    <span class="kas-label">Total Penerimaan</span>
                    <span class="kas-value text-right">Rp {{ number_format($kasHarian->total_penerimaan, 0, ',', '.') }}</span>
                </div>
                <div class="kas-item" style="border-top: 2px solid #3b82f6; padding-top: 8px; margin-top: 8px;">
                    <span class="kas-label text-bold">Saldo Akhir</span>
                    <span class="kas-value text-right text-bold">Rp {{ number_format($kasHarian->saldo_akhir, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Metode Pembayaran -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">METODE PEMBAYARAN</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Metode</th>
                        <th class="text-center">Jumlah Transaksi</th>
                        <th class="text-right">Total (Rp)</th>
                        <th class="text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metodePembayaran as $metode)
                    <tr>
                        <td>
                            <span class="badge badge-{{ strtolower($metode->metode_pembayaran) }}">
                                {{ $metode->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="text-center">{{ $metode->total }}</td>
                        <td class="text-right">Rp {{ number_format($metode->jumlah, 0, ',', '.') }}</td>
                        <td class="text-center">
                            {{ $totalPenjualan > 0 ? number_format(($metode->jumlah / $totalPenjualan) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                    <tr style="background: #f1f5f9;">
                        <td class="text-bold">TOTAL</td>
                        <td class="text-center text-bold">{{ $totalTransaksi }}</td>
                        <td class="text-right text-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                        <td class="text-center text-bold">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Detail Transaksi -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">DETAIL TRANSAKSI ({{ $transaksiHariIni->count() }})</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>No. Transaksi</th>
                        <th class="text-center">Waktu</th>
                        <th class="text-center">Metode</th>
                        <th class="text-center">Items</th>
                        <th class="text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiHariIni as $index => $trx)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $trx->no_transaksi }}</td>
                        <td class="text-center">{{ $trx->tanggal_transaksi->format('H:i') }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ strtolower($trx->metode_pembayaran) }}">
                                {{ $trx->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="text-center">{{ $trx->total_item }}</td>
                        <td class="text-right">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($transaksiHariIni->count() > 0)
                <tfoot>
                    <tr style="background: #f1f5f9;">
                        <td colspan="4" class="text-bold text-right">TOTAL:</td>
                        <td class="text-center text-bold">{{ $transaksiHariIni->sum('total_item') }}</td>
                        <td class="text-right text-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            Laporan ini dibuat secara otomatis oleh Sistem Kasir
        </div>
    </div>
</body>
</html>