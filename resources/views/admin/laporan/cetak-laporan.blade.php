<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Manajerial - {{ $periode }}</title>
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
            margin-bottom: 25px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .company-address {
            font-size: 11px;
            margin-bottom: 5px;
            color: #7f8c8d;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            color: #2c3e50;
        }

        .report-period {
            font-size: 13px;
            margin-top: 5px;
            color: #34495e;
        }

        /* Informasi Laporan */
        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }

        /* Statistik Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            margin: 8px 0;
        }

        .stat-label {
            font-size: 10px;
            color: #7f8c8d;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #34495e;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            text-align: center;
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

        /* Section */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #34495e;
            color: white;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13px;
        }

        /* Summary Box */
        .summary-box {
            background: #ecf0f1;
            border: 1px solid #bdc3c7;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #bdc3c7;
        }

        .summary-total {
            border-top: 2px solid #34495e;
            padding-top: 8px;
            margin-top: 8px;
            font-weight: bold;
            font-size: 13px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #34495e;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }

        .signature-area {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #34495e;
            width: 200px;
            display: inline-block;
        }

        /* Utilities */
        .page-break {
            page-break-before: always;
        }

        .mb-15 {
            margin-bottom: 15px;
        }

        .mt-15 {
            margin-top: 15px;
        }

        .bg-highlight {
            background-color: #fff3cd;
        }

        .text-success {
            color: #27ae60;
        }

        .text-danger {
            color: #e74c3c;
        }

        .text-warning {
            color: #f39c12;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">SMARTMART CAMPUS</div>
            <div class="company-address">
                Jl. Kampus No. 123, Jakarta Selatan | Telp: (021) 12345678 | Email: info@smartmartcampus.com
            </div>
            <div class="report-title">LAPORAN MANAJERIAL</div>
            <div class="report-period">{{ $periode }}</div>
            <div style="font-size: 10px; color: #95a5a6; margin-top: 5px;">
                Dicetak pada: {{ $tanggalCetak }}
            </div>
        </div>

        <!-- Ringkasan Eksekutif -->
        <div class="section">
            <div class="section-title">📊 RINGKASAN EKSEKUTIF</div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">TOTAL PENJUALAN</div>
                    <div class="stat-value text-success">Rp {{ number_format($statistik['total_penjualan'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">{{ $statistik['total_transaksi'] ?? 0 }} Transaksi</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">PROFIT KOTOR</div>
                    <div class="stat-value text-success">Rp {{ number_format($statistik['total_profit'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">
                        @php
                            $margin = ($statistik['total_penjualan'] ?? 0) > 0 ? 
                                number_format((($statistik['total_profit'] ?? 0) / ($statistik['total_penjualan'] ?? 1)) * 100, 1) : 0;
                        @endphp
                        {{ $margin }}% Margin
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">PROFIT BERSIH</div>
                    <div class="stat-value text-success">Rp {{ number_format($statistik['net_profit'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">After Expenses</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">RATA-RATA/TRANSAKSI</div>
                    <div class="stat-value text-warning">Rp {{ number_format($statistik['rata_rata_transaksi'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Per Transaction</div>
                </div>
            </div>

            <div class="summary-box">
                <div class="summary-row">
                    <span>Total Transaksi Tunai:</span>
                    <span class="bold">{{ $statistik['transaksi_tunai'] ?? 0 }} transaksi</span>
                </div>
                <div class="summary-row">
                    <span>Total Transaksi Non-Tunai:</span>
                    <span class="bold">{{ $statistik['transaksi_non_tunai'] ?? 0 }} transaksi</span>
                </div>
                <div class="summary-row">
                    <span>Total Pengeluaran Operasional:</span>
                    <span class="bold text-danger">Rp {{ number_format($statistik['total_pengeluaran'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row summary-total">
                    <span>PERTUMBUHAN PENJUALAN:</span>
                    <span class="bold {{ ($analisisKeuangan['pertumbuhan'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ ($analisisKeuangan['pertumbuhan'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($analisisKeuangan['pertumbuhan'] ?? 0, 1) }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="section">
            <div class="section-title">🔥 10 PRODUK TERLARIS</div>
            
            @if(count($produkTerlaris) > 0)
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Nama Produk</th>
                        <th width="15%">Harga Jual</th>
                        <th width="15%">Jumlah Terjual</th>
                        <th width="15%">Total Pendapatan</th>
                        <th width="15%">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produkTerlaris as $index => $produk)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $produk['nama_produk'] }}</td>
                        <td class="text-right">Rp {{ number_format($produk['harga_jual'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $produk['total_terjual'] }}</td>
                        <td class="text-right">Rp {{ number_format($produk['total_pendapatan'], 0, ',', '.') }}</td>
                        <td class="text-right text-success">
                            @php
                                $profitPerProduk = ($produk['harga_jual'] - ($produk['harga_beli'] ?? 0)) * $produk['total_terjual'];
                            @endphp
                            Rp {{ number_format($profitPerProduk, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-highlight">
                        <td colspan="3" class="text-right bold">TOTAL:</td>
                        <td class="text-center bold">
                            @php
                                $totalTerjual = array_sum(array_column($produkTerlaris, 'total_terjual'));
                            @endphp
                            {{ $totalTerjual }}
                        </td>
                        <td class="text-right bold">
                            @php
                                $totalPendapatan = array_sum(array_column($produkTerlaris, 'total_pendapatan'));
                            @endphp
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </td>
                        <td class="text-right bold text-success">
                            @php
                                $totalProfit = array_sum(array_map(function($p) { 
                                    return ($p['harga_jual'] - ($p['harga_beli'] ?? 0)) * $p['total_terjual']; 
                                }, $produkTerlaris));
                            @endphp
                            Rp {{ number_format($totalProfit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="text-center" style="padding: 20px; color: #7f8c8d;">
                Tidak ada data produk terlaris
            </div>
            @endif
        </div>

        <!-- Performa Kasir -->
        <div class="section">
            <div class="section-title">👥 PERFORMANSI KASIR</div>
            
            @if(count($performansiKasir) > 0)
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="45%">Nama Kasir</th>
                        <th width="15%">Total Transaksi</th>
                        <th width="20%">Total Penjualan</th>
                        <th width="15%">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($performansiKasir as $index => $kasir)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $kasir['nama_lengkap'] }}</td>
                        <td class="text-center">{{ $kasir['total_transaksi'] }}</td>
                        <td class="text-right">Rp {{ number_format($kasir['total_penjualan'], 0, ',', '.') }}</td>
                        <td class="text-right">
                            @php
                                $rataRata = $kasir['total_transaksi'] > 0 ? $kasir['total_penjualan'] / $kasir['total_transaksi'] : 0;
                            @endphp
                            Rp {{ number_format($rataRata, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-highlight">
                        <td colspan="2" class="text-right bold">TOTAL:</td>
                        <td class="text-center bold">
                            @php
                                $totalTransaksiKasir = array_sum(array_column($performansiKasir, 'total_transaksi'));
                            @endphp
                            {{ $totalTransaksiKasir }}
                        </td>
                        <td class="text-right bold">
                            @php
                                $totalPenjualanKasir = array_sum(array_column($performansiKasir, 'total_penjualan'));
                            @endphp
                            Rp {{ number_format($totalPenjualanKasir, 0, ',', '.') }}
                        </td>
                        <td class="text-right bold">
                            @php
                                $rataRataKasir = $totalTransaksiKasir > 0 ? $totalPenjualanKasir / $totalTransaksiKasir : 0;
                            @endphp
                            Rp {{ number_format($rataRataKasir, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="text-center" style="padding: 20px; color: #7f8c8d;">
                Tidak ada data performansi kasir
            </div>
            @endif
        </div>

        <!-- Analisis Metode Pembayaran -->
        @if(isset($analisisKeuangan['metode_pembayaran']) && count($analisisKeuangan['metode_pembayaran']) > 0)
        <div class="section">
            <div class="section-title">💳 ANALISIS METODE PEMBAYARAN</div>
            
            <table>
                <thead>
                    <tr>
                        <th width="60%">Metode Pembayaran</th>
                        <th width="20%">Jumlah Transaksi</th>
                        <th width="20%">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analisisKeuangan['metode_pembayaran'] as $metode)
                    <tr>
                        <td>{{ $metode['metode_pembayaran'] }}</td>
                        <td class="text-center">{{ $metode['total'] }}</td>
                        <td class="text-center">
                            {{ ($statistik['total_transaksi'] ?? 0) > 0 ? number_format(($metode['total'] / ($statistik['total_transaksi'] ?? 1)) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Tren Penjualan -->
        @if(count($trenPenjualan) > 0)
        <div class="section">
            <div class="section-title">📈 TREN PENJUALAN HARIAN</div>
            
            <table>
                <thead>
                    <tr>
                        <th width="30%">Tanggal</th>
                        <th width="25%">Total Penjualan</th>
                        <th width="25%">Jumlah Transaksi</th>
                        <th width="20%">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trenPenjualan as $tren)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tren['tanggal'])->translatedFormat('d F Y') }}</td>
                        <td class="text-right">Rp {{ number_format($tren['total_penjualan'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $tren['jumlah_transaksi'] ?? 1 }}</td>
                        <td class="text-right">Rp {{ number_format($tren['total_penjualan'] / ($tren['jumlah_transaksi'] ?? 1), 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Rekomendasi & Insight -->
        <div class="section">
            <div class="section-title">💡 REKOMENDASI MANAJERIAL</div>
            
            <div class="summary-box">
                <div class="summary-row">
                    <span class="bold">1. Optimasi Stok Produk Terlaris:</span>
                    <span>Tingkatkan stok untuk 5 produk teratas</span>
                </div>
                <div class="summary-row">
                    <span class="bold">2. Pengembangan Tim:</span>
                    <span>Berikan reward untuk kasir berprestasi</span>
                </div>
                <div class="summary-row">
                    <span class="bold">3. Efisiensi Operasional:</span>
                    <span>Review pengeluaran untuk meningkatkan net profit</span>
                </div>
                <div class="summary-row">
                    <span class="bold">4. Strategi Promosi:</span>
                    <span>Fokus pada metode pembayaran yang dominan</span>
                </div>
            </div>
        </div>

        <!-- Footer & Tanda Tangan -->
        <div class="footer">
            <div class="signature-area">
                <div class="signature-box">
                    <div>Disiapkan Oleh,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top: 5px; font-size: 11px;">Manager Operasional</div>
                </div>
                <div class="signature-box">
                    <div>Disetujui Oleh,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top: 5px; font-size: 11px;">Direktur</div>
                </div>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                <strong>Catatan:</strong> Laporan ini dibuat secara otomatis oleh sistem. Data dapat berubah sesuai dengan update transaksi terbaru.
            </div>
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