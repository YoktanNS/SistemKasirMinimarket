<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas Harian - {{ $tanggalLaporan }}</title>
    <style>
        /* Reset dan base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .report-period {
            font-size: 12px;
            margin-top: 5px;
        }

        /* Informasi Kas */
        .kas-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-box {
            border: 1px solid #000;
            padding: 10px;
        }

        .info-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        /* Summary */
        .summary {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-row {
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .signature {
            text-align: center;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 200px;
            display: inline-block;
        }

        /* Utilities */
        .page-break {
            page-break-before: always;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .bg-light {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">SMARTMART CAMPUS</div>
            <div class="company-address">
                Jl. Kampus No. 123, Jakarta Selatan<br>
                Telp: (021) 12345678 | Email: info@smartmartcampus.com
            </div>
            <div class="report-title">LAPORAN KAS HARIAN</div>
            <div class="report-period">Periode: {{ $tanggalLaporan }}</div>
        </div>

        <!-- Informasi Kas -->
        <div class="kas-info">
            <div class="info-box">
                <div class="info-title">INFORMASI KAS</div>
                <div><strong>Tanggal:</strong> {{ $tanggalLaporan }}</div>
                <div><strong>Status:</strong> {{ $kasHarian->status }}</div>
                <div><strong>Kasir:</strong> {{ $kasHarian->user->nama_lengkap ?? ($kasHarian->user->name ?? 'Tidak diketahui') }}</div>
                <div><strong>Dibuka:</strong> {{ $kasHarian->created_at->format('H:i') }}</div>
                @if($kasHarian->status == 'Closed')
                <div><strong>Ditutup:</strong> {{ $kasHarian->waktu_tutup ? $kasHarian->waktu_tutup->format('H:i') : '-' }}</div>
                @endif
            </div>

            <div class="info-box">
                <div class="info-title">RINGKASAN TRANSAKSI</div>
                <div><strong>Total Transaksi:</strong> {{ number_format($totalTransaksi) }}</div>
                <div><strong>Total Item Terjual:</strong> {{ number_format($totalItemTerjual) }}</div>
                <div><strong>Total Penjualan:</strong> Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                <div><strong>Rata-rata/Transaksi:</strong> Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Ringkasan Kas -->
        <div class="summary">
            <div class="info-title">RINGKASAN KAS</div>
            <div class="summary-row">
                <span>Saldo Awal Kas:</span>
                <span class="bold">Rp {{ number_format($kasHarian->saldo_awal, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Penerimaan Tunai:</span>
                <span class="bold">Rp {{ number_format($kasHarian->penerimaan_tunai, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Penerimaan Non-Tunai:</span>
                <span class="bold">Rp {{ number_format($kasHarian->penerimaan_non_tunai, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Total Penerimaan:</span>
                <span class="bold">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Pengeluaran:</span>
                <span class="bold">Rp {{ number_format($kasHarian->pengeluaran, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row total-row">
                <span>SALDO AKHIR KAS:</span>
                <span class="bold">Rp {{ number_format($kasHarian->saldo_akhir, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Detail Transaksi -->
        @if($transaksi->count() > 0)
        <div class="mb-10 mt-10">
            <div class="info-title">DETAIL TRANSAKSI</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">No. Transaksi</th>
                        <th width="10%">Waktu</th>
                        <th width="15%">Metode</th>
                        <th width="10%">Items</th>
                        <th width="15%">Subtotal</th>
                        <th width="15%">Diskon</th>
                        <th width="15%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi as $index => $trx)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $trx->no_transaksi }}</td>
                        <td>{{ $trx->tanggal_transaksi->format('H:i') }}</td>
                        <td>{{ $trx->metode_pembayaran }}</td>
                        <td class="text-center">{{ $trx->items->sum('jumlah') }}</td>
                        <td class="text-right">Rp {{ number_format($trx->subtotal ?? $trx->total_bayar, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($trx->diskon ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right bold">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <td colspan="4" class="text-right bold">TOTAL:</td>
                        <td class="text-center bold">{{ $totalItemTerjual }}</td>
                        <td class="text-right bold">Rp {{ number_format($transaksi->sum('subtotal') ?? $transaksi->sum('total_bayar'), 0, ',', '.') }}</td>
                        <td class="text-right bold">Rp {{ number_format($transaksi->sum('diskon') ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Metode Pembayaran -->
        <div class="mb-10">
            <div class="info-title">RINCIAN METODE PEMBAYARAN</div>
            <table>
                <thead>
                    <tr>
                        <th>Metode Pembayaran</th>
                        <th width="15%">Jumlah Transaksi</th>
                        <th width="25%">Total Nilai</th>
                        <th width="15%">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metodePembayaran as $metode)
                    <tr>
                        <td>{{ $metode->metode_pembayaran }}</td>
                        <td class="text-center">{{ $metode->total }}</td>
                        <td class="text-right">Rp {{ number_format($metode->jumlah, 0, ',', '.') }}</td>
                        <td class="text-right">
                            {{ $totalPenjualan > 0 ? number_format(($metode->jumlah / $totalPenjualan) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="mb-10 mt-10">
            <div class="info-title">DETAIL TRANSAKSI</div>
            <p class="text-center">Tidak ada transaksi pada tanggal ini</p>
        </div>
        @endif

        <!-- Pengeluaran -->
        @if($pengeluaran->count() > 0)
        <div class="mb-10">
            <div class="info-title">PENGELUARAN HARIAN</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Keterangan</th>
                        <th width="15%">Kategori</th>
                        <th width="20%">Jumlah</th>
                        <th width="15%">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengeluaran as $index => $peng)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $peng->keterangan }}</td>
                        <td>{{ $peng->kategori }}</td>
                        <td class="text-right">Rp {{ number_format($peng->jumlah, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $peng->created_at->format('H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <td colspan="3" class="text-right bold">TOTAL PENGELUARAN:</td>
                        <td class="text-right bold">Rp {{ number_format($pengeluaran->sum('jumlah'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        <!-- Footer dan Tanda Tangan -->
        <div class="footer">
            <div></div>
            <div class="signature">
                <div>Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
                <div>Kasir</div>
                <div class="signature-line"></div>
                <div>({{ $kasHarian->user->nama_lengkap ?? ($kasHarian->user->name ?? '....................') }})</div>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="mt-10" style="font-size: 10px; border-top: 1px solid #000; padding-top: 10px;">
            <strong>Keterangan:</strong><br>
            • Laporan ini dicetak secara otomatis oleh sistem<br>
            • Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}<br>
            • Status Kas: {{ $kasHarian->status }}
        </div>
    </div>

    <script>
        // Auto print ketika halaman loaded
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>