<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - {{ $startDate }} sampai {{ $endDate }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .summary-card {
            flex: 1;
            min-width: 120px;
            margin: 0 3px 10px 3px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        .summary-card h3 {
            margin: 0 0 3px 0;
            font-size: 10px;
            color: #7f8c8d;
        }
        .summary-card p {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 8px;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 8px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #2c3e50 !important;
            color: white;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .section-title {
            background-color: #ecf0f1;
            padding: 8px;
            margin: 15px 0 8px 0;
            border-left: 4px solid #3498db;
            font-weight: bold;
            color: #2c3e50;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-tunai { background-color: #d4edda; color: #155724; }
        .badge-debit { background-color: #cce7ff; color: #004085; }
        .badge-qris { background-color: #e8d7f7; color: #4a235a; }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p>Sistem Manajemen Kasir</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span><strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</span>
            <span><strong>Total Transaksi:</strong> {{ $summary['total_transaksi'] }} transaksi</span>
        </div>
        <div class="info-row">
            <span><strong>Total Hari:</strong> {{ $transaksi->groupBy('tanggal_transaksi')->count() }} hari</span>
            <span><strong>Total Item:</strong> {{ number_format($summary['total_item'], 0, ',', '.') }} item</span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card" style="border-left-color: #3498db;">
            <h3>Total Penjualan</h3>
            <p>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #27ae60;">
            <h3>Total Transaksi</h3>
            <p>{{ number_format($summary['total_transaksi'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #8e44ad;">
            <h3>Rata-rata/Transaksi</h3>
            <p>Rp {{ number_format($summary['rata_rata'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #e67e22;">
            <h3>Total Item Terjual</h3>
            <p>{{ number_format($summary['total_item'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Produk Terlaris -->
    @if($produkTerlaris->isNotEmpty())
    <div class="section-title">5 PRODUK TERLARIS</div>
    <table>
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th width="45%">Nama Produk</th>
                <th width="15%" class="text-center">Jumlah Terjual</th>
                <th width="20%" class="text-right">Total Penjualan</th>
                <th width="15%" class="text-right">Rata-rata/Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produkTerlaris as $index => $produk)
            <tr>
                <td class="text-center">
                    <span style="display: inline-block; width: 18px; height: 18px; line-height: 18px; border-radius: 50%; 
                        @if($index == 0) background-color: #ffd700; color: #000;
                        @elseif($index == 1) background-color: #c0c0c0; color: #000;
                        @elseif($index == 2) background-color: #cd7f32; color: #000;
                        @else background-color: #ecf0f1; color: #2c3e50; @endif">
                        {{ $index + 1 }}
                    </span>
                </td>
                <td>{{ $produk->nama_produk }}</td>
                <td class="text-center">{{ number_format($produk->total_terjual, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</td>
                <td class="text-right">
                    @php
                        $rataHarga = $produk->total_terjual > 0 ? $produk->total_penjualan / $produk->total_terjual : 0;
                    @endphp
                    Rp {{ number_format($rataHarga, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2"><strong>Total</strong></td>
                <td class="text-center"><strong>{{ number_format($produkTerlaris->sum('total_terjual'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($produkTerlaris->sum('total_penjualan'), 0, ',', '.') }}</strong></td>
                <td class="text-right">
                    @php
                        $totalTerjual = $produkTerlaris->sum('total_terjual');
                        $totalPenjualan = $produkTerlaris->sum('total_penjualan');
                        $rataRataHarga = $totalTerjual > 0 ? $totalPenjualan / $totalTerjual : 0;
                    @endphp
                    <strong>Rp {{ number_format($rataRataHarga, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Metode Pembayaran -->
    @if($metodePembayaran->isNotEmpty())
    <div class="section-title">METODE PEMBAYARAN</div>
    <table>
        <thead>
            <tr>
                <th width="60%">Metode Pembayaran</th>
                <th width="20%" class="text-center">Jumlah Transaksi</th>
                <th width="20%" class="text-right">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metodePembayaran as $metode => $data)
            <tr>
                <td>
                    <span class="badge 
                        @if($metode === 'Tunai') badge-tunai
                        @elseif($metode === 'Debit') badge-debit
                        @elseif($metode === 'QRIS') badge-qris
                        @else badge-default @endif">
                        {{ $metode }}
                    </span>
                </td>
                <td class="text-center">{{ $data['count'] }}</td>
                <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Detail Transaksi -->
    <div class="section-title">DETAIL TRANSAKSI PENJUALAN</div>
    <table>
        <thead>
            <tr>
                <th width="12%">No. Transaksi</th>
                <th width="10%">Tanggal</th>
                <th width="12%">Kasir</th>
                <th width="10%">Metode Bayar</th>
                <th width="8%" class="text-center">Total Item</th>
                <th width="12%" class="text-right">Subtotal</th>
                <th width="10%" class="text-right">Diskon</th>
                <th width="12%" class="text-right">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $trx)
            <tr>
                <td>{{ $trx->no_transaksi }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}</td>
                <td>{{ $trx->kasir->nama ?? 'N/A' }}</td>
                <td>
                    <span class="badge 
                        @if($trx->metode_pembayaran === 'Tunai') badge-tunai
                        @elseif($trx->metode_pembayaran === 'Debit') badge-debit
                        @elseif($trx->metode_pembayaran === 'QRIS') badge-qris
                        @else badge-default @endif">
                        {{ $trx->metode_pembayaran }}
                    </span>
                </td>
                <td class="text-center">{{ $trx->total_item }}</td>
                <td class="text-right">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                <td class="text-right">- Rp {{ number_format($trx->diskon, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data transaksi untuk periode yang dipilih</td>
            </tr>
            @endforelse
        </tbody>
        @if($transaksi->isNotEmpty())
        <tfoot>
            <tr class="total-row">
                <td colspan="4"><strong>Total</strong></td>
                <td class="text-center"><strong>{{ $summary['total_item'] }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($summary['total_subtotal'], 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>- Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Ringkasan -->
    <div class="section-title">RINGKASAN</div>
    <table>
        <tbody>
            <tr>
                <td width="30%"><strong>Total Transaksi</strong></td>
                <td width="70%">{{ number_format($summary['total_transaksi'], 0, ',', '.') }} transaksi</td>
            </tr>
            <tr>
                <td><strong>Total Item Terjual</strong></td>
                <td>{{ number_format($summary['total_item'], 0, ',', '.') }} item</td>
            </tr>
            <tr>
                <td><strong>Total Subtotal</strong></td>
                <td>Rp {{ number_format($summary['total_subtotal'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Diskon</strong></td>
                <td>- Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Penjualan Bersih</strong></td>
                <td><strong>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Rata-rata per Transaksi</strong></td>
                <td>Rp {{ number_format($summary['rata_rata'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh Sistem Manajemen Kasir</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>