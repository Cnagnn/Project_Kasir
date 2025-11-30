<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan per Produk</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
        h1 { font-size: 18px; margin:0 0 6px 0; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #999; padding:4px 6px; }
        th { background:#f0f0f0; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Pendapatan per Produk</h1>
    <p style="margin:0;">Rentang: {{ $rangeLabel }}</p>
    <p style="margin:0 0 10px 0;">Dihasilkan: {{ $generatedAt->format('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th style="width:32px">#</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th class="text-end">Jumlah Terjual</th>
                <th class="text-end">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td class="text-end">{{ number_format($row['qty'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row['revenue'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:14px; color:#666;">Tidak ada data pada rentang ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total</th>
                <th class="text-end">{{ number_format($grandQty ?? 0, 0, ',', '.') }}</th>
                <th class="text-end">Rp {{ number_format($grandRevenue ?? 0, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
    <p style="margin-top:12px; font-size:10px; color:#555;">Disusun otomatis dari data transaksi.</p>
</body>
</html>
