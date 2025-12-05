<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
        h1 { font-size: 18px; margin:0 0 6px 0; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #999; padding:4px 6px; }
        th { background:#f0f0f0; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Pembelian</h1>
    <p style="margin:0;">Rentang: {{ $rangeLabel }}</p>
    <p style="margin:0 0 10px 0;">Dihasilkan: {{ $generatedAt->format('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th style="width:32px">#</th>
                <th>Tanggal</th>
                <th>Nomor</th>
                <th>Nama Produk</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Harga</th>
                <th class="text-end">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                    <td>{{ $row['stock_id'] }}</td>
                    <td>{{ $row['product_name'] }}</td>
                    <td class="text-center">{{ number_format($row['qty'], 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row['price'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row['subtotal'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:14px; color:#666;">Tidak ada data pada rentang ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-end">Grand Total</th>
                <th class="text-end">Rp {{ number_format($grandTotal ?? 0, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
    <p style="margin-top:12px; font-size:10px; color:#555;">Disusun otomatis dari data pembelian stok.</p>
</body>
</html>
