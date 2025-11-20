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

    $today = now();
    $weekStart = now()->copy()->startOfWeek();
    $weekEnd = now()->copy()->endOfWeek();
    $monthStart = now()->copy()->startOfMonth();
    $timeframeOptions = [
        ['label' => 'Hari ini', 'range' => $today->translatedFormat('d F Y')],
        ['label' => 'Minggu ini', 'range' => $weekStart->translatedFormat('d F Y') . ' - ' . $weekEnd->translatedFormat('d F Y')],
        ['label' => 'Bulan ini', 'range' => $monthStart->translatedFormat('d F Y') . ' - ' . $today->translatedFormat('d F Y')],
    ];

    $widgetToggles = [
        'Penjualan Hari Ini',
        'Jumlah Transaksi Hari Ini',
        'Produk Aktif',
        'Kategori',
    ];

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

<style>
    .dashboard-filter-btn {
        min-width: 210px;
    }
    .widget-check {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 0.4rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #22c55e;
        color: #fff;
        font-size: 0.9rem;
    }
    .widget-check:not(.widget-check-active) {
        background: #e5e7eb;
        color: #6b7280;
    }
    .widget-check:not(.widget-check-active) i {
        display: none;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-rounded shadow-sm mb-4">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h3 class="mb-1">Dashboard Kasir</h3>
                    <p class="text-muted mb-0">Ringkasan singkat operasional hari ini.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <div class="dropdown">
                        <button class="btn btn-primary dashboard-filter-btn d-flex justify-content-between align-items-center" type="button" id="timeframeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Hari ini ({{ $today->translatedFormat('d F Y') }})</span>
                            <i class="mdi mdi-menu-down ms-2"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="timeframeDropdown">
                            @foreach ($timeframeOptions as $index => $option)
                                <a class="dropdown-item d-flex justify-content-between align-items-center {{ $index === 0 ? 'active fw-semibold' : '' }}" href="#">
                                    <span>{{ $option['label'] }}</span>
                                    <small class="text-muted ms-2">{{ $option['range'] }}</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-primary d-flex align-items-center" type="button" id="widgetDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-view-grid"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end text-start p-2" style="min-width: 220px;" aria-labelledby="widgetDropdown">
                            <a href="#" class="dropdown-item d-flex align-items-center gap-2 py-2" data-widget="penjualan">
                                <span class="widget-check widget-check-active">
                                    <i class="mdi mdi-check"></i>
                                </span>
                                <span>Penjualan Hari Ini</span>
                            </a>
                            <a href="#" class="dropdown-item d-flex align-items-center gap-2 py-2" data-widget="transaksi">
                                <span class="widget-check widget-check-active">
                                    <i class="mdi mdi-check"></i>
                                </span>
                                <span>Jumlah Transaksi Hari Ini</span>
                            </a>
                            <a href="#" class="dropdown-item d-flex align-items-center gap-2 py-2" data-widget="produk">
                                <span class="widget-check widget-check-active">
                                    <i class="mdi mdi-check"></i>
                                </span>
                                <span>Produk Aktif</span>
                            </a>
                            <a href="#" class="dropdown-item d-flex align-items-center gap-2 py-2" data-widget="kategori">
                                <span class="widget-check widget-check-active">
                                    <i class="mdi mdi-check"></i>
                                </span>
                                <span>Kategori</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item text-primary text-center" id="reset-widgets">Reset ke Default</a>
                        </div>
                    </div>
                </div>
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
            <div class="col-md-6 col-xl-3" id="card-penjualan">
                <div class="card card-rounded shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Penjualan Hari Ini</p>
                        <h3 class="fw-bold mb-2">Rp. {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</h3>
                        {{-- <span class="small {{ $dashboardStats[0]['trend_class'] }}">{{ $dashboardStats[0]['trend']}}</span> --}}
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3" id="card-transaksi">
                <div class="card card-rounded shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Jumlah Transaksi Hari Ini</p>
                        <h3 class="fw-bold mb-2">{{ $transaksiHariIni }}</h3>
                        {{-- <span class="small {{ $dashboardStats[0]['trend_class'] }}">{{ $dashboardStats[0]['trend']}}</span> --}}
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3" id="card-produk">
                <div class="card card-rounded shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Produk Aktif</p>
                        <h3 class="fw-bold mb-2">{{ $produkAktif }}</h3>
                        {{-- <span class="small {{ $dashboardStats[0]['trend_class'] }}">{{ $dashboardStats[0]['trend']}}</span> --}}
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3" id="card-kategori">
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
            <div class="col-lg-8">
                <div class="card card-rounded h-100">
                    <div class="card-body">
                        <h4 class="card-title card-title-dash mb-4">Grafik Penjualan</h4>
                        <canvas id="salesChart" height="80"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-rounded h-100">
                    <div class="card-body">
                        <h4 class="card-title card-title-dash mb-4">Kategori Terlaris</h4>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Load widget visibility from localStorage
    let widgetVisibility = JSON.parse(localStorage.getItem('widgetVisibility')) || {
        penjualan: true,
        transaksi: true,
        produk: true,
        kategori: true
    };

    // Function to update visibility
    function updateWidgetVisibility() {
        $('#card-penjualan').toggle(widgetVisibility.penjualan);
        $('#card-transaksi').toggle(widgetVisibility.transaksi);
        $('#card-produk').toggle(widgetVisibility.produk);
        $('#card-kategori').toggle(widgetVisibility.kategori);

        // Update checkmarks
        $('[data-widget="penjualan"] .widget-check').toggleClass('widget-check-active', widgetVisibility.penjualan);
        $('[data-widget="transaksi"] .widget-check').toggleClass('widget-check-active', widgetVisibility.transaksi);
        $('[data-widget="produk"] .widget-check').toggleClass('widget-check-active', widgetVisibility.produk);
        $('[data-widget="kategori"] .widget-check').toggleClass('widget-check-active', widgetVisibility.kategori);
    }

    // Initial update
    updateWidgetVisibility();

    // Toggle on click
    $('[data-widget]').on('click', function(e) {
        e.preventDefault();
        let widget = $(this).data('widget');
        widgetVisibility[widget] = !widgetVisibility[widget];
        localStorage.setItem('widgetVisibility', JSON.stringify(widgetVisibility));
        updateWidgetVisibility();
    });

    // Reset
    $('#reset-widgets').on('click', function(e) {
        e.preventDefault();
        widgetVisibility = {
            penjualan: true,
            transaksi: true,
            produk: true,
            kategori: true
        };
        localStorage.setItem('widgetVisibility', JSON.stringify(widgetVisibility));
        updateWidgetVisibility();
    });
});
</script>
<script>
    // Grafik Penjualan (Line Chart)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Penjualan (Rp)',
                data: [3200000, 4100000, 3800000, 5200000, 4800000, 6100000, 3500000],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        }
                    }
                }
            }
        }
    });

    // Grafik Kategori (Doughnut Chart)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Makanan', 'Minuman', 'Bumbu Dapur', 'Kebutuhan RT', 'Lainnya'],
            datasets: [{
                data: [35, 28, 18, 12, 7],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush