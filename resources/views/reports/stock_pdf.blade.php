<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stock Produk</title>
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
    <h1>Laporan Stock Produk</h1>
    <p style="margin:0 0 10px 0;">Dihasilkan: {{ $generatedAt->format('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th style="width:32px">#</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th class="text-end">Total Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td class="text-end">{{ number_format($row['total_remaining'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:14px; color:#666;">Tidak ada data stok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <p style="margin-top:12px; font-size:10px; color:#555;">Dicetak otomatis untuk kebutuhan arsip internal.</p>
</body>
</html>
