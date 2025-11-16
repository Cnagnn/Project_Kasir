@extends('layouts.admin')

@section('content')

@php
    if (!isset($dashboardStats)) {
        $dashboardStats = [
            ['label' => 'Penjualan Hari Ini', 'value' => 'Rp35.500.000', 'trend' => '+8%', 'trend_class' => 'text-success'],
            ['label' => 'Transaksi Hari Ini', 'value' => '126', 'trend' => '±0%', 'trend_class' => 'text-muted'],
            ['label' => 'Produk Aktif', 'value' => '342', 'trend' => '+12 barang', 'trend_class' => 'text-success'],
            ['label' => 'Kategori', 'value' => '18', 'trend' => '+1 kategori', 'trend_class' => 'text-primary'],
        ];
    }

    if (!isset($recentTransactions)) {
        $recentTransactions = collect([
            ['invoice' => 'INV-1009', 'user' => 'Umum', 'total' => 'Rp850.000', 'time' => '10:45'],
            ['invoice' => 'INV-1008', 'user' => 'Member A', 'total' => 'Rp1.200.000', 'time' => '10:17'],
            ['invoice' => 'INV-1007', 'user' => 'Member B', 'total' => 'Rp640.000', 'time' => '09:52'],
            ['invoice' => 'INV-1006', 'user' => 'Umum', 'total' => 'Rp330.000', 'time' => '09:40'],
            ['invoice' => 'INV-1005', 'user' => 'Member C', 'total' => 'Rp1.980.000', 'time' => '09:10'],
        ]);
    }

    if (!isset($lowStockProducts)) {
        $lowStockProducts = collect([
            ['name' => 'Gula Pasir 1Kg', 'category' => 'Bahan Pokok', 'stock' => 6],
            ['name' => 'Kopi Kapal Api', 'category' => 'Minuman', 'stock' => 8],
            ['name' => 'Teh Botol', 'category' => 'Minuman', 'stock' => 4],
            ['name' => 'Minyak Goreng 1L', 'category' => 'Bahan Pokok', 'stock' => 3],
        ]);
    }

    if (!isset($shiftNotes)) {
        $shiftNotes = [
            'Promo buy 2 get 1 untuk kopi masih berjalan sampai akhir minggu.',
            'Pastikan stok rokok dihitung ulang sebelum penutupan kasir.',
            'Konfirmasi retur barang supplier A sebelum pukul 15.00.',
        ];
    }
@endphp

<div class="row">
    <div class="col-sm-12">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h3 class="mb-1">Dashboard Kasir</h3>
                <p class="text-muted mb-0">Ringkasan singkat operasional hari ini.</p>
            </div>
            <div class="mt-3 mt-md-0 text-md-end">
                <span class="fw-semibold">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>

        {{-- <div class="row g-3 mb-4">
            @foreach ($dashboardStats as $stat)
                <div class="col-md-6 col-xl-3">
                    <div class="card card-rounded shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">{{ $stat['label'] }}</p>
                            <h3 class="fw-bold mb-2">{{ $stat['value'] }}</h3>
                            <span class="small {{ $stat['trend_class'] }}">{{ $stat['trend'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div> --}}

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card card-rounded shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Penjualan Hari Ini</p>
                        <h3 class="fw-bold mb-2">Rp. {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</h3>
                        {{-- <span class="small {{ $dashboardStats[0]['trend_class'] }}">{{ $dashboardStats[0]['trend']}}</span> --}}
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card card-rounded shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Jumlah Transaksi Hari Ini</p>
                        <h3 class="fw-bold mb-2">{{ $transaksiHariIni }}</h3>
                        {{-- <span class="small {{ $dashboardStats[0]['trend_class'] }}">{{ $dashboardStats[0]['trend']}}</span> --}}
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card card-rounded shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Produk Aktif</p>
                        <h3 class="fw-bold mb-2">{{ $produkAktif }}</h3>
                        {{-- <span class="small {{ $dashboardStats[0]['trend_class'] }}">{{ $dashboardStats[0]['trend']}}</span> --}}
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card card-rounded shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Kategori</p>
                        <h3 class="fw-bold mb-2">{{ $totalKategori }}</h3>
                        {{-- <span class="small {{ $dashboardStats[0]['trend_class'] }}">{{ $dashboardStats[0]['trend']}}</span> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card card-rounded h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title card-title-dash mb-0">Transaksi Terbaru</h4>
                            <a href="#" class="text-primary small">Lihat semua</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Operator</th>
                                        <th>Total</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transaksiTerbaru as $transaction)
                                        <tr>
                                            <td class="fw-semibold">{{ $transaction['invoice'] ?? $transaction->invoice_number }}</td>
                                            <td>{{ $transaction->user->name ?? '-' }}</td>
                                            <td>Rp. {{ number_format($transaction->total_payment ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ $transaction->created_at->format('H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Belum ada transaksi hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

@endsection