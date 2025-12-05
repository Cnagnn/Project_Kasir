<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 57mm;
            padding: 4mm 3mm 6mm;
            margin: 0 auto;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .store-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .store-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .store-info {
            font-size: 9px;
            line-height: 1.4;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .info-label {
            display: inline-block;
            width: 30%;
        }

        .info-value {
            display: inline-block;
            width: 70%;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        .items-table thead {
            border-bottom: 1px dashed #000;
        }

        .items-table thead th {
            font-weight: bold;
            font-size: 10px;
            padding: 3px 0;
            text-align: left;
        }

        .items-table tbody {
            border-bottom: 1px dashed #000;
        }

        .items-table tbody td {
            padding: 3px 0;
            font-size: 10px;
        }

        .items-table .col-item {
            width: 46%;
            text-align: left;
        }

        .items-table .col-qty {
            width: 14%;
            text-align: center;
        }

        .items-table .col-price {
            width: 20%;
            text-align: right;
        }

        .items-table .col-subtotal {
            width: 20%;
            text-align: right;
        }

        .summary-table {
            width: 100%;
            margin-top: 6px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 10px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 11px;
            margin-top: 3px;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: 9px;
            line-height: 1.4;
        }

        /* Print Styles */
        @media print {
            @page {
                size: 57mm auto;
                margin: 0;
            }

            body {
                width: 57mm;
                padding: 4mm 3mm 6mm;
            }
        }
    </style>
</head>
<body>
    
    <!-- Store Header -->
    <div class="store-header">
        <div class="store-name">TOKO KASIR</div>
        <div class="store-info">
            Jl. Contoh No.123, Kota<br>
            Telp: 0812-3456-7890
        </div>
    </div>

    <hr class="divider">

    <!-- Transaction Info -->
    <div class="transaction-info">
        <div class="info-row">
            <span class="info-label">No Invoice</span>
            <span class="info-value">: {{ $transaction->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">: {{ \Carbon\Carbon::parse($transaction->created_at ?? $transaction->transaction_date)->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kasir</span>
            <span class="info-value">: {{ optional($transaction->user)->name ?? 'Admin' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Metode</span>
            <span class="info-value">: {{ $transaction->payment_method }}</span>
        </div>
    </div>

    <hr class="divider">

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-item">Item</th>
                <th class="col-qty">Qty</th>
                <th class="col-price">Harga</th>
                <th class="col-subtotal">Sub</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td class="col-item">{{ $item['name'] }}</td>
                <td class="col-qty">{{ $item['qty'] }}</td>
                <td class="col-price">{{ number_format($item['price'], 0, ',', '.') }}</td>
                <td class="col-subtotal">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary-table">
        <div class="summary-row total">
            <span>TOTAL</span>
            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Status</span>
            <span>{{ strtoupper($transaction->payment_status) }}</span>
        </div>
    </div>

    <hr class="divider">

    <!-- Footer -->
    <div class="footer">
        Terima kasih atas kunjungan Anda!<br>
        Barang yang sudah dibeli<br>
        tidak dapat ditukar/dikembalikan
    </div>

    <script>
        // Auto-print with delay to ensure page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
        
        // Close window after print dialog is closed
        window.addEventListener('afterprint', function() {
            // Optional: close window after print
            // Uncomment if you want auto-close after print
            // setTimeout(function() { window.close(); }, 1000);
        });
    </script>

</body>
</html>
