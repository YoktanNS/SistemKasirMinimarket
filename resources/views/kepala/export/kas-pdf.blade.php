<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kas - {{ $startDate }} sampai {{ $endDate }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
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
        }
        .summary-card {
            flex: 1;
            margin: 0 5px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #7f8c8d;
        }
        .summary-card p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
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
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KAS</h1>
        <p>Sistem Manajemen Kasir</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span><strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</span>
            <span><strong>Total Hari:</strong> {{ $laporanKas->count() }} hari</span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card" style="border-left-color: #3498db;">
            <h3>Total Pendapatan</h3>
            <p>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #27ae60;">
            <h3>Total Transaksi</h3>
            <p>{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #8e44ad;">
            <h3>Rata-rata/Transaksi</h3>
            <p>Rp {{ number_format($rataRata, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #e67e22;">
            <h3>Hari Aktif</h3>
            <p>{{ $hariAktif }}</p>
        </div>
    </div>

    <!-- Detail Laporan -->
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Pendapatan</th>
                <th class="text-right">Rata-rata/Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanKas as $laporan)
            <tr>
                <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}</td>
                <td class="text-center">{{ $laporan->jumlah_transaksi }}</td>
                <td class="text-right">Rp {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($laporan->rata_rata, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data transaksi untuk periode yang dipilih</td>
            </tr>
            @endforelse
        </tbody>
        @if($laporanKas->isNotEmpty())
        <tfoot>
            <tr class="total-row">
                <td><strong>Total</strong></td>
                <td class="text-center"><strong>{{ $totalTransaksi }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($rataRata, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Chart Section (akan ditampilkan sebagai tabel sederhana) -->
    @if($laporanKas->isNotEmpty())
    <div style="margin-top: 30px;">
        <h3 style="text-align: center; color: #2c3e50; margin-bottom: 15px;">Rekapitulasi Harian</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-center">Transaksi</th>
                    <th class="text-right">Pendapatan</th>
                    <th class="text-center">% dari Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanKas as $laporan)
                @php
                    $percentage = $totalPendapatan > 0 ? ($laporan->total_pendapatan / $totalPendapatan) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M') }}</td>
                    <td class="text-center">{{ $laporan->jumlah_transaksi }}</td>
                    <td class="text-right">Rp {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($percentage, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak otomatis oleh Sistem Manajemen Kasir</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>