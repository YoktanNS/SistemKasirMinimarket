<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok - {{ \Carbon\Carbon::now()->format('d F Y') }}</title>
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
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .summary-card {
            flex: 1;
            min-width: 120px;
            margin: 0 3px 8px 3px;
            padding: 6px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        .summary-card h3 {
            margin: 0 0 3px 0;
            font-size: 9px;
            color: #7f8c8d;
        }
        .summary-card p {
            margin: 0;
            font-size: 11px;
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
            padding: 6px;
            margin: 12px 0 6px 0;
            border-left: 4px solid #3498db;
            font-weight: bold;
            color: #2c3e50;
            font-size: 10px;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-aman { background-color: #d4edda; color: #155724; }
        .badge-menipis { background-color: #fff3cd; color: #856404; }
        .badge-habis { background-color: #f8d7da; color: #721c24; }
        .page-break {
            page-break-after: always;
        }
        .summary-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .summary-box h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
        }
        .summary-box p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STOK PRODUK</h1>
        <p>Sistem Manajemen Minimarket</p>
        <p>Periode: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span><strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</span>
            <span><strong>Total Produk:</strong> {{ number_format($summary['total_produk'], 0, ',', '.') }} produk</span>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <h3>Total Nilai Investasi Stok</h3>
        <p>Rp {{ number_format($summary['total_nilai_stok'], 0, ',', '.') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card" style="border-left-color: #3498db;">
            <h3>Total Produk</h3>
            <p>{{ number_format($summary['total_produk'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #8e44ad;">
            <h3>Total Stok</h3>
            <p>{{ number_format($summary['total_stok_semua'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #27ae60;">
            <h3>Stok Aman</h3>
            <p>{{ number_format($summary['stok_aman'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #f39c12;">
            <h3>Stok Menipis</h3>
            <p>{{ number_format($summary['stok_menipis_count'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left-color: #e74c3c;">
            <h3>Stok Habis</h3>
            <p>{{ number_format($summary['stok_habis'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Stok Menipis -->
    @if($stokMenipis->isNotEmpty())
    <div class="section-title">STOK MENIPIS (PERLU PERHATIAN)</div>
    <table>
        <thead>
            <tr>
                <th width="25%">Nama Produk</th>
                <th width="15%">Kategori</th>
                <th width="20%">Supplier</th>
                <th width="10%" class="text-center">Stok Tersedia</th>
                <th width="10%" class="text-center">Stok Minimum</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stokMenipis as $produk)
            <tr>
                <td>{{ $produk->nama_produk }}</td>
                <td>{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $produk->supplier->nama_supplier ?? '-' }}</td>
                <td class="text-center">{{ number_format($produk->stok_tersedia, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($produk->stok_minimum, 0, ',', '.') }}</td>
                <td>
                    @if($produk->stok_tersedia == 0)
                        <span class="badge badge-habis">HABIS</span>
                    @else
                        <span class="badge badge-menipis">MENIPIS</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3"><strong>Total Produk Perlu Perhatian</strong></td>
                <td class="text-center"><strong>{{ $stokMenipis->count() }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Produk Nilai Tertinggi -->
    @if($produkNilaiTertinggi->isNotEmpty())
    <div class="section-title">10 PRODUK DENGAN NILAI STOK TERTINGGI</div>
    <table>
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th width="30%">Nama Produk</th>
                <th width="10%" class="text-center">Stok</th>
                <th width="15%" class="text-right">Harga Jual</th>
                <th width="20%" class="text-right">Total Nilai</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produkNilaiTertinggi as $index => $produk)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $produk->nama_produk }}</td>
                <td class="text-center">{{ number_format($produk->stok_tersedia, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($produk->total_nilai, 0, ',', '.') }}</td>
                <td>
                    @if($produk->stok_tersedia == 0)
                        <span class="badge badge-habis">HABIS</span>
                    @elseif($produk->stok_tersedia <= 5) <!-- Anggap stok minimum 5 -->
                        <span class="badge badge-menipis">MENIPIS</span>
                    @else
                        <span class="badge badge-aman">AMAN</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4"><strong>Total Nilai 10 Produk Tertinggi</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($produkNilaiTertinggi->sum('total_nilai'), 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Stok Per Kategori -->
    @if($stokPerKategori->isNotEmpty())
    <div class="section-title">STOK BERDASARKAN KATEGORI</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Kategori</th>
                <th width="15%" class="text-center">Jumlah Produk</th>
                <th width="15%" class="text-center">Total Stok</th>
                <th width="20%" class="text-right">Total Nilai</th>
                <th width="20%" class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stokPerKategori as $kategori)
            @php
                $percentage = ($kategori->total_nilai_stok / $summary['total_nilai_stok']) * 100;
            @endphp
            <tr>
                <td>{{ $kategori->nama_kategori }}</td>
                <td class="text-center">{{ number_format($kategori->jumlah_produk, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($kategori->total_stok, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($kategori->total_nilai_stok, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($percentage, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td><strong>Total Semua Kategori</strong></td>
                <td class="text-center"><strong>{{ $stokPerKategori->sum('jumlah_produk') }}</strong></td>
                <td class="text-center"><strong>{{ $stokPerKategori->sum('total_stok') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($stokPerKategori->sum('total_nilai_stok'), 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>100%</strong></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Ringkasan -->
    <div class="section-title">RINGKASAN LAPORAN STOK</div>
    <table>
        <tbody>
            <tr>
                <td width="30%"><strong>Total Seluruh Produk</strong></td>
                <td width="70%">{{ number_format($summary['total_produk'], 0, ',', '.') }} produk</td>
            </tr>
            <tr>
                <td><strong>Total Seluruh Stok Barang</strong></td>
                <td>{{ number_format($summary['total_stok_semua'], 0, ',', '.') }} item</td>
            </tr>
            <tr>
                <td><strong>Produk dengan Stok Aman</strong></td>
                <td>{{ number_format($summary['stok_aman'], 0, ',', '.') }} produk</td>
            </tr>
            <tr>
                <td><strong>Produk dengan Stok Menipis</strong></td>
                <td>{{ number_format($summary['stok_menipis_count'], 0, ',', '.') }} produk</td>
            </tr>
            <tr>
                <td><strong>Produk dengan Stok Habis</strong></td>
                <td>{{ number_format($summary['stok_habis'], 0, ',', '.') }} produk</td>
            </tr>
            <tr>
                <td><strong>Total Nilai Investasi Stok</strong></td>
                <td><strong>Rp {{ number_format($summary['total_nilai_stok'], 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh Sistem Manajemen Minimarket</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>