@extends('layouts.admin')

@section('page-title', 'Dashboard Kasir')
@section('page-description', 'Ringkasan singkat operasional hari ini')

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
    /* Shadow styling untuk card agar lebih menarik */
    .card.card-rounded {
        border-radius: 0.75rem;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.06);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .card.card-rounded:hover {
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12), 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    .card.card-rounded .card-body {
        padding: 1.25rem 1.25rem;
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

        <!-- Card untuk kontrol filter dan widget -->
        <div class="card card-rounded shadow-sm mb-4">
            <div class="card-body">
                                <!-- Tabs ringkasan ditempatkan di dalam card kontrol ini -->
                                <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="laporan-tab" data-bs-toggle="tab" data-bs-target="#laporan" type="button" role="tab" aria-controls="laporan" aria-selected="false">Laporan</button>
                                    </li>
                                </ul>
                                <div class="tab-content pt-3" id="dashboardTabContent">
                                  <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <h4 class="mb-1">Overview</h4>
                                            <p class="text-muted mb-0">Ringkasan singkat operasional.</p>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                            <div class="dropdown">
                                                <button class="btn btn-primary dashboard-filter-btn d-flex justify-content-between align-items-center" type="button" id="timeframeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span id="timeframeLabel">Hari ini ({{ $today->translatedFormat('d F Y') }})</span>
                                                    <i class="mdi mdi-menu-down ms-2"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="timeframeDropdown">
                                                    @foreach ($timeframeOptions as $index => $option)
                                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ $index === 0 ? 'active fw-semibold' : '' }}" href="#" data-timeframe="{{ $index === 0 ? 'day' : ($index === 1 ? 'week' : 'month') }}">
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
                                    <div class="row g-3 mt-1">
                                      <div class="col-md-6 col-xl-3" id="card-penjualan">
                                          <div class="card card-rounded shadow-sm h-100">
                                              <div class="card-body">
                                                  <p class="text-muted small mb-1" id="penjualanLabel">Penjualan Hari Ini</p>
                                                  <h3 class="fw-bold mb-2">Rp. <span id="penjualanValue">{{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</span></h3>
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-md-6 col-xl-3" id="card-transaksi">
                                          <div class="card card-rounded shadow-sm h-100">
                                              <div class="card-body">
                                                  <p class="text-muted small mb-1" id="transaksiLabel">Jumlah Transaksi Hari Ini</p>
                                                  <h3 class="fw-bold mb-2" id="transaksiValue">{{ $transaksiHariIni }}</h3>
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-md-6 col-xl-3" id="card-produk">
                                          <div class="card card-rounded shadow-sm h-100">
                                              <div class="card-body">
                                                  <p class="text-muted small mb-1">Produk Aktif</p>
                                                  <h3 class="fw-bold mb-2">{{ $produkAktif }}</h3>
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-md-6 col-xl-3" id="card-kategori">
                                          <div class="card card-rounded shadow-sm h-100">
                                              <div class="card-body">
                                                  <p class="text-muted small mb-1">Kategori</p>
                                                  <h3 class="fw-bold mb-2">{{ $totalKategori }}</h3>
                                              </div>
                                          </div>
                                      </div>
                                    </div>
                                  </div>
                                    <div class="tab-pane fade" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
                                        <div class="mb-3">
                                            <h4 class="mb-1">Laporan</h4>
                                            <p class="text-muted mb-0">Pilih jenis laporan yang ingin Anda lihat.</p>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-4">
                                                <a href="{{ route('reports.stock.print') }}" target="_blank" class="text-decoration-none">
                                                    <div class="card card-rounded h-100 border-primary">
                                                        <div class="card-body text-center">
                                                            <i class="mdi mdi-package-variant text-primary" style="font-size: 3rem;"></i>
                                                            <h5 class="mt-3 mb-2">Laporan Stock</h5>
                                                            <p class="text-muted small mb-0">Lihat detail stok barang tersedia</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPendapatanProduk">
                                                    <div class="card card-rounded h-100 border-success">
                                                        <div class="card-body text-center">
                                                            <i class="mdi mdi-chart-line text-success" style="font-size: 3rem;"></i>
                                                            <h5 class="mt-3 mb-2">Laporan Pendapatan per Produk</h5>
                                                            <p class="text-muted small mb-0">Analisis pendapatan setiap produk</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPendapatanInvoice">
                                                    <div class="card card-rounded h-100 border-info">
                                                        <div class="card-body text-center">
                                                            <i class="mdi mdi-file-document text-info" style="font-size: 3rem;"></i>
                                                            <h5 class="mt-3 mb-2">Laporan Pendapatan per Invoice</h5>
                                                            <p class="text-muted small mb-0">Rincian pendapatan per transaksi</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                
            </div>
        </div>

        <!-- Modal Rentang Tanggal - Laporan Pendapatan per Produk -->
        <div class="modal fade" id="modalPendapatanProduk" tabindex="-1" aria-labelledby="modalPendapatanProdukLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPendapatanProdukLabel">
                            <i class="mdi mdi-chart-line text-success me-2"></i>
                            Laporan Pendapatan per Produk
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formPendapatanProduk" method="GET" action="{{ route('reports.productRevenue.print') }}" target="_blank">
                        <div class="modal-body">
                            <p class="text-muted mb-3">Pilih rentang tanggal untuk melihat laporan pendapatan per produk.</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="startDateProduk" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="startDateProduk" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="endDateProduk" class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="endDateProduk" name="end_date" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-file-pdf-box me-1"></i> Lihat Laporan PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Rentang Tanggal - Laporan Pendapatan per Invoice -->
        <div class="modal fade" id="modalPendapatanInvoice" tabindex="-1" aria-labelledby="modalPendapatanInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPendapatanInvoiceLabel">
                            <i class="mdi mdi-file-document text-info me-2"></i>
                            Laporan Pendapatan per Invoice
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formPendapatanInvoice" method="GET" action="{{ route('reports.invoiceRevenue.print') }}" target="_blank">
                        <div class="modal-body">
                            <p class="text-muted mb-3">Pilih rentang tanggal untuk melihat laporan pendapatan per invoice.</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="startDateInvoice" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="startDateInvoice" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="endDateInvoice" class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="endDateInvoice" name="end_date" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-file-pdf-box me-1"></i> Lihat Laporan PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                                <div class="card card-rounded h-100">
                                        <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h4 class="card-title card-title-dash mb-0">Grafik Penjualan</h4>
                                                        <div class="dropdown">
                                                                <button class="btn btn-primary dashboard-filter-btn d-flex justify-content-between align-items-center" type="button" id="salesRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <span id="salesRangeLabel">Quantity Produk</span>
                                                                        <i class="mdi mdi-menu-down ms-2"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="salesRangeDropdown" style="min-width: 210px;">
                                                                        <li><a class="dropdown-item active" href="#" data-range="quantity">Quantity Produk</a></li>
                                                                        <li><a class="dropdown-item" href="#" data-range="nominal">Nominal Penjualan</a></li>
                                                                </ul>
                                                        </div>
                                                </div>
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

    // Timeframe dropdown - update KPI cards
    $('#timeframeDropdown').next('.dropdown-menu').find('a[data-timeframe]').on('click', function(e){
        e.preventDefault();
        const tf = $(this).data('timeframe');
        // active state
        $('#timeframeDropdown').next('.dropdown-menu').find('a').removeClass('active fw-semibold');
        $(this).addClass('active fw-semibold');
        // fetch metrics
        fetch(`{{ route('dashboard.metrics') }}?timeframe=${tf}`)
            .then(r => r.json())
            .then(json => {
                // format penjualan
                const formattedSales = json.penjualan.toLocaleString('id-ID');
                $('#penjualanValue').text(formattedSales);
                $('#transaksiValue').text(json.transaksi);
                // update label text
                const labelDateRange = (() => {
                    if (tf === 'day') {
                        return new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric'});
                    }
                    if (tf === 'week') {
                        // start monday to today
                        const now = new Date();
                        const dayIdx = now.getDay(); // 0=Sun
                        const mondayOffset = (dayIdx === 0 ? -6 : 1 - dayIdx); // distance to Monday
                        const monday = new Date(now);
                        monday.setDate(now.getDate() + mondayOffset);
                        const startStr = monday.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric'});
                        const endStr = now.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric'});
                        return `${startStr} - ${endStr}`;
                    }
                    if (tf === 'month') {
                        const now = new Date();
                        const first = new Date(now.getFullYear(), now.getMonth(), 1);
                        const startStr = first.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric'});
                        const endStr = now.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric'});
                        return `${startStr} - ${endStr}`;
                    }
                    return '';
                })();
                $('#timeframeLabel').text(`${json.label} (${labelDateRange})`);
                // update headings sesuai timeframe
                const suffix = json.label === 'Hari ini' ? 'Hari Ini' : (json.label === 'Minggu ini' ? 'Minggu Ini' : 'Bulan Ini');
                $('#penjualanLabel').text(`Penjualan ${suffix}`);
                $('#transaksiLabel').text(`Jumlah Transaksi ${suffix}`);
            })
            .catch(() => {
                // fallback do nothing / could show toast
            });
    });
});
</script>
<script>
    // Grafik Penjualan (Line Chart)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    let quantityLabels = [];
    let quantityDatasets = [];
    let nominalLabels = [];
    let nominalData = [];
    let currentMode = 'quantity'; // default mode

    function formatTick(value, max, mode) {
        if (mode === 'quantity') {
            return value.toLocaleString('id-ID');
        }
        // mode nominal
        if (max < 1000000) {
            return 'Rp ' + value.toLocaleString('id-ID');
        }
        if (max < 100000000) { // gunakan juta
            if (value < 1000000) {
                return 'Rp ' + value.toLocaleString('id-ID');
            }
            const v = value / 1000000;
            const decimals = v >= 10 ? 0 : 1;
            return 'Rp ' + v.toFixed(decimals) + ' jt';
        }
        // di atas 100 juta pakai M (miliar)
        if (value < 1000000000) {
            return 'Rp ' + (value / 1000000).toFixed(0) + ' jt';
        }
        return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
    }

    // Helpers untuk gradient dinamis berdasarkan mode
    function buildGradient(ctx, rgbBase) {
        const g = ctx.createLinearGradient(0, 0, 0, 240);
        g.addColorStop(0, `rgba(${rgbBase},0.35)`);
        g.addColorStop(0.5, `rgba(${rgbBase},0.18)`);
        g.addColorStop(1, `rgba(${rgbBase},0)`);
        return g;
    }
    let currentGradient = buildGradient(salesCtx, '34,197,94'); // default green

    // Shadow plugin
    const shadowPlugin = {
        id: 'lineShadow',
        beforeDraw(chart, args, opts) {
            const {ctx, chartArea} = chart;
            ctx.save();
            ctx.shadowColor = 'rgba(34,197,94,0.35)';
            ctx.shadowBlur = 12;
            ctx.shadowOffsetY = 6;
        },
        afterDraw(chart){ chart.ctx.restore(); }
    };

    Chart.register(shadowPlugin);

    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: {
                duration: 900,
                easing: 'easeOutQuart'
            },
            interaction: { mode: 'nearest', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { usePointStyle: true, boxWidth: 10, font: { weight: '600', size: 11 }, padding: 10 }
                },
                tooltip: {
                    backgroundColor: 'rgba(31,41,55,0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { weight: '600', size: 13 },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: (item) => {
                            const label = item.dataset.label || '';
                            if (currentMode === 'quantity') {
                                return label + ': ' + item.parsed.y.toLocaleString('id-ID') + ' produk';
                            }
                            return label + ': Rp ' + item.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: '500' }, color: '#374151' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.06)', drawBorder: false },
                    ticks: {
                        padding: 8,
                        font: { weight: '500' },
                        color: '#374151',
                        callback: function(value){
                            const max = this.max;
                            return formatTick(value, max, currentMode);
                        }
                    }
                }
            }
        }
    });

    function applyQuantity() {
        currentMode = 'quantity';
        salesChart.data.labels = quantityLabels;
        salesChart.data.datasets = quantityDatasets;
        salesChart.update();
    }

    function applyNominal() {
        currentMode = 'nominal';
        currentGradient = buildGradient(salesCtx, '37,99,235'); // blue
        salesChart.data.labels = nominalLabels;
        salesChart.data.datasets = [{
            label: 'Nominal Penjualan',
            data: nominalData,
            borderColor: 'rgba(37,99,235,1)',
            backgroundColor: currentGradient,
            tension: 0.45,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 7,
            pointBorderWidth: 2,
            pointBackgroundColor: '#fff',
            pointBorderColor: 'rgba(37,99,235,1)'
        }];
        salesChart.update();
    }

    function fetchSalesData() {
        fetch('{{ route('dashboard.salesProductData') }}')
            .then(r => r.json())
            .then(json => {
                quantityLabels = json.quantity.labels;
                quantityDatasets = json.quantity.datasets;
                nominalLabels = json.nominal.labels;
                nominalData = json.nominal.data;
                applyQuantity(); // default tampilan quantity
            })
            .catch(() => {
                // fallback placeholder jika error
                quantityLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                quantityDatasets = [];
                nominalLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                nominalData = new Array(12).fill(0);
                applyQuantity();
            });
    }

    fetchSalesData();

    document.querySelectorAll('#salesRangeDropdown + .dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const range = this.getAttribute('data-range');
            document.querySelectorAll('#salesRangeDropdown + .dropdown-menu .dropdown-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('salesRangeLabel').textContent = this.textContent.trim();
            if (range === 'quantity') {
                applyQuantity();
            } else {
                applyNominal();
            }
        });
    });

    // Grafik Kategori (Doughnut Chart)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#6366F1', '#10B981', '#F59E0B', '#EF4444', '#0EA5E9'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ctx.label + ': ' + ctx.parsed + '%'
                    }
                }
            },
            cutout: '55%'
        }
    });

    function fetchCategoryData(){
        fetch('{{ route('dashboard.categoryData') }}')
            .then(r => r.json())
            .then(json => {
                categoryChart.data.labels = json.labels;
                categoryChart.data.datasets[0].data = json.data;
                categoryChart.update();
            })
            .catch(() => {
                categoryChart.data.labels = ['Tidak ada data'];
                categoryChart.data.datasets[0].data = [0];
                categoryChart.update();
            });
    }
    fetchCategoryData();
</script>
@endpush