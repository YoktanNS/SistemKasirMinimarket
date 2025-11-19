<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaksi->no_transaksi }} - SmartMart Campus</title>
    
    <!-- Thermal Printer Optimized Styles -->
    <style>
        /* Reset dan base styles */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box;
            font-weight: normal;
        }
        
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
                margin-top: 5mm;
                margin-bottom: 5mm;
            }
            
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 80mm !important;
                font-family: 'Courier New', Courier, monospace !important;
                font-size: 12px !important;
                line-height: 1.2 !important;
                background: white !important;
                color: black !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .no-print, 
            .button-container,
            .print-message {
                display: none !important;
            }
            
            /* Optimasi untuk thermal printer */
            .thermal-optimized {
                font-family: 'Courier New', Courier, monospace !important;
                font-size: 12px !important;
                line-height: 1.2 !important;
            }
            
            /* Hindari page break di tengah item */
            .items-table tr {
                page-break-inside: avoid;
            }
        }
        
        /* Screen Styles */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.2;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
            color: black;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .company-address {
            font-size: 10px;
            margin-top: 2px;
        }
        
        /* Transaction Info */
        .transaction-info {
            margin-bottom: 8px;
            padding: 0 2px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .items-table td {
            padding: 1px 0;
            vertical-align: top;
        }
        
        .item-name {
            font-weight: bold;
            padding-bottom: 0;
        }
        
        .item-details {
            padding-top: 0;
            font-size: 10px;
            color: #555;
        }
        
        .item-price {
            text-align: right;
            white-space: nowrap;
        }
        
        /* Summary Section */
        .summary {
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .total-row {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 3px;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 10px;
        }
        
        /* Barcode */
        .barcode-container {
            text-align: center;
            margin: 8px 0;
            padding: 5px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        
        .barcode {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 28px;
            letter-spacing: 2px;
        }
        
        .barcode-text {
            font-size: 9px;
            margin-top: 2px;
            font-family: 'Courier New', Courier, monospace;
        }
        
        /* Utility Classes */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .text-sm { font-size: 10px; }
        .text-xs { font-size: 9px; }
        
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        
        .spacer {
            height: 3px;
        }

        /* Button Styles (Non-print) */
        .print-message {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 12px;
        }
        
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 15px;
            padding: 0 10px;
        }
        
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .btn-back {
            background: #3b82f6;
            color: white;
        }
        
        .btn-close {
            background: #6b7280;
            color: white;
        }
        
        .btn-download {
            background: #8b5cf6;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-print:hover { background: linear-gradient(135deg, #059669, #047857); }
        .btn-back:hover { background: #2563eb; }
        .btn-close:hover { background: #4b5563; }
        .btn-download:hover { background: #7c3aed; }
        
        /* Thermal printer simulation for screen */
        @media screen {
            body {
                background: #f5f5f5;
                padding: 20px 10px;
            }
            
            .receipt-paper {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                padding: 15px;
                margin: 0 auto;
                max-width: 80mm;
            }
        }
    </style>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Thermal printer friendly meta tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="format-detection" content="telephone=no">
</head>
<body>
    <div class="receipt-paper thermal-optimized">
        <!-- Print Message -->
        <div class="print-message no-print">
            <i class="fas fa-info-circle"></i>
            Struk akan otomatis print. Jika tidak, klik tombol Print Struk.
        </div>

        <!-- Header -->
        <div class="header">
            <div class="company-name">SmartMart Campus</div>
            <div class="company-address">
                Universitas Example Campus<br>
                Jl. Kampus No. 123, Jakarta<br>
                Telp: (021) 1234-5678
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <div class="info-row">
                <span>No. Transaksi:</span>
                <span class="text-bold">{{ $transaksi->no_transaksi }}</span>
            </div>
            <div class="info-row">
                <span>Tanggal/Waktu:</span>
                <span>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span>Kasir:</span>
                <span>{{ $transaksi->kasir->name ?? 'System' }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Items -->
        <table class="items-table">
            <tbody>
                @foreach($transaksi->items as $index => $item)
                <tr>
                    <td colspan="2" class="item-name">
                        {{ $item->nama_produk }}
                    </td>
                </tr>
                <tr>
                    <td class="item-details">
                        {{ $item->qty }} x Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                        @if($item->barcode)
                        <br><span class="text-xs">#{{ $item->barcode }}</span>
                        @endif
                    </td>
                    <td class="item-price text-bold">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @if(!$loop->last)
                <tr><td colspan="2" style="height: 2px;"></td></tr>
                @endif
                @endforeach
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($transaksi->diskon > 0)
            <div class="summary-row">
                <span>Diskon:</span>
                <span>- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="summary-row total-row">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
            </div>
            
            <div class="spacer"></div>
            
            <div class="summary-row">
                <span>{{ $transaksi->metode_pembayaran }}:</span>
                <span>Rp {{ number_format($transaksi->jumlah_uang, 0, ',', '.') }}</span>
            </div>
            
            @if($transaksi->kembalian > 0)
            <div class="summary-row text-bold">
                <span>KEMBALI:</span>
                <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <!-- Barcode -->
        <div class="barcode-container">
            <div class="barcode">*{{ $transaksi->no_transaksi }}*</div>
            <div class="barcode-text">{{ $transaksi->no_transaksi }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="text-bold text-uppercase">Terima Kasih</div>
            <div class="text-sm">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</div>
            <div class="text-xs">Struk ini sebagai bukti pembayaran yang sah</div>
            <div class="text-xs" style="margin-top: 3px;">
                {{ $transaksi->tanggal_transaksi->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>

    <!-- Print Controls -->
    <div class="no-print button-container">
        <button onclick="handlePrint()" class="btn btn-print">
            <i class="fas fa-print"></i>Print Struk
        </button>
        
        <button onclick="downloadAsPDF()" class="btn btn-download">
            <i class="fas fa-download"></i>Download PDF
        </button>
        
        <button onclick="goBackToKasir()" class="btn btn-back">
            <i class="fas fa-arrow-left"></i>Transaksi Baru
        </button>
        
        <button onclick="closeWindow()" class="btn btn-close">
            <i class="fas fa-times"></i>Tutup Window
        </button>
    </div>

    <script>
        // Enhanced printing functionality
        function handlePrint() {
            const printMessage = document.querySelector('.print-message');
            if (printMessage) {
                printMessage.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mempersiapkan print...';
            }
            
            setTimeout(() => {
                window.print();
            }, 100);
        }

        // Download as PDF (basic implementation)
        function downloadAsPDF() {
            // Fallback - buka print dialog untuk save as PDF
            alert('Fitur download PDF akan datang. Gunakan "Print to PDF" di dialog print untuk sekarang.');
            window.print();
        }

        // Navigate back to kasir
        function goBackToKasir() {
            try {
                if (window.opener && !window.opener.closed) {
                    window.close();
                } else {
                    window.location.href = "{{ route('kasir.index') }}";
                }
            } catch (e) {
                window.location.href = "{{ route('kasir.index') }}";
            }
        }

        // Close window
        function closeWindow() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.close();
            }
        }

        // Auto print dengan delay untuk memastikan page loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                // Hanya auto-print jika di mobile/tablet atau environment tertentu
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                const shouldAutoPrint = isMobile || new URLSearchParams(window.location.search).has('autoprint');
                
                if (shouldAutoPrint) {
                    handlePrint();
                }
            }, 1000);
        });

        // Handle after print event
        window.addEventListener('afterprint', function() {
            console.log('Print completed or cancelled');
            
            // Tampilkan pesan sukses
            const printMessage = document.querySelector('.print-message');
            if (printMessage) {
                printMessage.innerHTML = '<i class="fas fa-check-circle"></i> Print selesai!';
                printMessage.style.background = '#d1fae5';
                printMessage.style.borderColor = '#a7f3d0';
                
                setTimeout(() => {
                    printMessage.style.display = 'none';
                }, 3000);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P atau Cmd+P untuk print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                handlePrint();
            }
            
            // Escape untuk close
            if (e.key === 'Escape') {
                closeWindow();
            }
            
            // Enter untuk back to kasir
            if (e.key === 'Enter' && !e.ctrlKey && !e.metaKey) {
                goBackToKasir();
            }
        });

        // Prevent accidental navigation
        window.addEventListener('beforeunload', function(e) {
            if (!window.isPrinting) {
                // Optional: Confirm before leaving
                // e.preventDefault();
                // e.returnValue = '';
            }
        });
    </script>
</body>
</html>