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

        <div class="row g-3 mb-4">
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
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
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
                                        <th>Pembeli</th>
                                        <th>Total</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentTransactions as $transaction)
                                        <tr>
                                            <td class="fw-semibold">{{ $transaction['invoice'] ?? $transaction->invoice_number }}</td>
                                            <td>{{ $transaction['user'] ?? optional($transaction->users)->name ?? '-' }}</td>
                                            <td>{{ $transaction['total'] ?? number_format($transaction->total_payment ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ $transaction['time'] ?? optional($transaction->transaction_date)->format('H:i') }}</td>
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

            <div class="col-lg-4">
                <div class="card card-rounded h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title card-title-dash mb-0">Notifikasi Stok</h4>
                            <span class="badge bg-warning text-dark">{{ $lowStockProducts->count() }} produk</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            @forelse ($lowStockProducts as $product)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-semibold">{{ $product['name'] ?? $product->name }}</p>
                                        <small class="text-muted">{{ $product['category'] ?? optional($product->category)->name ?? '-' }}</small>
                                    </div>
                                    <span class="badge bg-light text-danger border border-danger">{{ $product['stock'] ?? $product->remaining_stock }} pcs</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center">Stok aman</li>
                            @endforelse
                        </ul>
                        <a href="{{ route('stock.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-3">Kelola stok</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <div class="card card-rounded h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title card-title-dash mb-0">Catatan Shift</h4>
                            <span class="text-muted small">Update terakhir {{ now()->format('H:i') }}</span>
                        </div>
                        <ul class="list-unstyled mb-0">
                            @foreach ($shiftNotes as $note)
                                <li class="d-flex mb-3">
                                    <span class="badge bg-primary me-3 rounded-circle" style="width: 10px; height: 10px; min-width: 10px;"></span>
                                    <span>{{ $note }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-rounded h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title card-title-dash mb-0">Aksi Cepat</h4>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <a href="{{ route('selling.index') }}" class="btn btn-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                    <i class="mdi mdi-cart-plus mb-1"></i>
                                    Transaksi Baru
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('product.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                    <i class="mdi mdi-package-variant mb-1"></i>
                                    Data Produk
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('purchasing.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                    <i class="mdi mdi-truck-delivery mb-1"></i>
                                    Tambah Stok
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                    <i class="mdi mdi-account-group mb-1"></i>
                                    Tim Kasir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection