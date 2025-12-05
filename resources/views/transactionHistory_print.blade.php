<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; width:57mm; padding:4mm 3mm 6px; margin:0 auto; box-sizing:border-box; font-size:11px; line-height:1.25; }
        h4 { margin:0 0 4px; font-size:14px; text-align:center; }
        .center { text-align:center; }
        .right { text-align:right; }
        .mb-0 { margin-bottom:0; }
        .mb-1 { margin-bottom:4px; }
        .mb-2 { margin-bottom:8px; }
        hr { border:none; border-top:1px dashed #000; margin:6px 0; }
        table { width:100%; border-collapse:collapse; }
        thead tr { border-bottom:1px dashed #000; }
        tbody tr:last-child { border-bottom:1px dashed #000; }
        th { font-weight:700; font-size:11px; padding:2px 0; letter-spacing:.5px; }
        td { padding:2px 0; font-size:11px; }
        .name { text-align:left; }
        .qty { text-align:center; }
        .price, .subtotal { text-align:right; }
        .totals-row td { padding-top:4px; }
        .footer { text-align:center; margin-top:8px; font-size:10px; }
        @media print { 
            @page { size:57mm auto; margin:0; } 
            body { width:57mm; } 
        }
    </style>
</head>
<body>
    
    <div class="center mb-2">
        <h4 class="mb-0">TOKO KASIR</h4>
        <div style="font-size:10px;">Jl. Contoh No.123, Kota<br>Telp: 0812-3456-7890</div>
    </div>
    <hr>
    <div style="font-size:11px;">
        No: <strong>{{ $transaction->invoice_number }}</strong><br>
        Tgl: <strong>{{ \Carbon\Carbon::parse($transaction->created_at ?? $transaction->transaction_date)->format('d/m/Y H:i') }}</strong><br>
        Kasir: <strong>{{ optional($transaction->user)->name ?? 'Admin' }}</strong><br>
        Metode: <strong>{{ $transaction->payment_method }}</strong>
    </div>
    <hr>
    <table>
        <thead>
            <tr>
                <th class="name" style="width:46%">Item</th>
                <th class="qty" style="width:14%">Qty</th>
                <th class="price" style="width:20%">Harga</th>
                <th class="subtotal" style="width:20%">Sub</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $it)
                <tr>
                    <td class="name">{{ $it['name'] }}</td>
                    <td class="qty">{{ $it['qty'] }}</td>
                    <td class="price">{{ number_format($it['price'],0,',','.') }}</td>
                    <td class="subtotal">{{ number_format($it['subtotal'],0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table style="width:100%; margin-top:4px;">
        <tr>
            <td style="width:60%;">TOTAL</td>
            <td class="right" style="width:40%;"><strong>Rp {{ number_format($total,0,',','.') }}</strong></td>
        </tr>
        <tr>
            <td>STATUS</td>
            <td class="right">{{ strtoupper($transaction->payment_status) }}</td>
        </tr>
    </table>
    <hr>
    <div class="footer">
        Terima kasih atas kunjungan Anda!<br>
        Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.
    </div>
    
    <script>
        // Auto-print with delay to ensure page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
        
        // Optional: close window after print
        window.addEventListener('afterprint', function() {
            setTimeout(function() { 
                window.close(); 
            }, 600);
        });
    </script>
    
</body>
</html>
