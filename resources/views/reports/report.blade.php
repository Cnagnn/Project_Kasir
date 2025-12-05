
@extends('layouts.admin')

@section('page-title', 'Laporan')
@section('page-description', 'Pilih jenis laporan yang ingin Anda lihat')

@section('content')
<style>
    /* Shadow styling untuk card agar sama seperti dashboard */
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
    .report-card-inner {
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .report-card-inner:hover {
        transform: scale(1.02);
    }
    .report-card-inner i {
        transition: color 0.2s ease;
    }
    .report-card-inner:hover i {
        color: #1F3BB3 !important;
    }
</style>

<div class="row">
  <!-- Card Laporan Stock -->
  <div class="col-md-3">
    <div class="card card-rounded mb-4 h-100">
      <div class="card-body p-3">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalLaporanStock">
          <div class="card report-card-inner h-100 border-primary">
            <div class="card-body text-center p-3">
              <i class="mdi mdi-package-variant text-dark" style="font-size: 2.5rem;"></i>
              <h6 class="mt-2 mb-1">Laporan Stock</h6>
              <p class="text-muted small mb-0" style="font-size: 0.75rem;">Lihat detail stok barang tersedia</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- Card Laporan Pendapatan per Produk -->
  <div class="col-md-3">
    <div class="card card-rounded mb-4 h-100">
      <div class="card-body p-3">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPendapatanProduk">
          <div class="card report-card-inner h-100 border-success">
            <div class="card-body text-center p-3">
              <i class="mdi mdi-chart-line text-dark" style="font-size: 2.5rem;"></i>
              <h6 class="mt-2 mb-1">Laporan Pendapatan per Produk</h6>
              <p class="text-muted small mb-0" style="font-size: 0.75rem;">Analisis pendapatan setiap produk</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- Card Laporan Pendapatan per Invoice -->
  <div class="col-md-3">
    <div class="card card-rounded mb-4 h-100">
      <div class="card-body p-3">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPendapatanInvoice">
          <div class="card report-card-inner h-100 border-info">
            <div class="card-body text-center p-3">
              <i class="mdi mdi-file-document text-dark" style="font-size: 2.5rem;"></i>
              <h6 class="mt-2 mb-1">Laporan Pendapatan per Invoice</h6>
              <p class="text-muted small mb-0" style="font-size: 0.75rem;">Rincian pendapatan per transaksi</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- Card Laporan Pembelian -->
  <div class="col-md-3">
    <div class="card card-rounded mb-4 h-100">
      <div class="card-body p-3">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalLaporanPembelian">
          <div class="card report-card-inner h-100 border-warning">
            <div class="card-body text-center p-3">
              <i class="mdi mdi-cart text-dark" style="font-size: 2.5rem;"></i>
              <h6 class="mt-2 mb-1">Laporan Pembelian</h6>
              <p class="text-muted small mb-0" style="font-size: 0.75rem;">Rincian pembelian stok barang</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Rentang Tanggal - Laporan Stock -->
<div class="modal fade" id="modalLaporanStock" tabindex="-1" aria-labelledby="modalLaporanStockLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLaporanStockLabel">
          <i class="mdi mdi-package-variant text-white me-2"></i>
          Laporan Stock Produk
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formLaporanStock" method="GET" action="{{ route('reports.stock.print') }}" target="_blank">
        <div class="modal-body">
          <p class="text-muted mb-3">Pilih rentang tanggal untuk melihat stok historis produk.</p>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="startDateStock" class="form-label">Tanggal Mulai</label>
              <input type="date" class="form-control" id="startDateStock" name="start_date">
            </div>
            <div class="col-md-6">
              <label for="endDateStock" class="form-label">Tanggal Selesai</label>
              <input type="date" class="form-control" id="endDateStock" name="end_date">
            </div>
          </div>
          <div class="alert alert-info mt-3 mb-0" role="alert">
            <small>
              <i class="mdi mdi-information"></i>
              <strong>Info:</strong> Kosongkan tanggal untuk melihat stok terkini. Isi tanggal untuk melihat stok historis pada periode tertentu.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat Laporan PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Rentang Tanggal - Pendapatan per Produk -->
<div class="modal fade" id="modalPendapatanProduk" tabindex="-1" aria-labelledby="modalPendapatanProdukLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPendapatanProdukLabel">
          <i class="mdi mdi-chart-line text-white me-2"></i>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat Laporan PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Rentang Tanggal - Pendapatan per Invoice -->
<div class="modal fade" id="modalPendapatanInvoice" tabindex="-1" aria-labelledby="modalPendapatanInvoiceLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPendapatanInvoiceLabel">
          <i class="mdi mdi-file-document text-white me-2"></i>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat Laporan PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Rentang Tanggal - Laporan Pembelian -->
<div class="modal fade" id="modalLaporanPembelian" tabindex="-1" aria-labelledby="modalLaporanPembelianLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLaporanPembelianLabel">
          <i class="mdi mdi-cart text-white me-2"></i>
          Laporan Pembelian
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formLaporanPembelian" method="GET" action="{{ route('reports.purchasing.print') }}" target="_blank">
        <div class="modal-body">
          <p class="text-muted mb-3">Pilih rentang tanggal untuk melihat laporan pembelian stok barang.</p>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="startDatePembelian" class="form-label">Tanggal Mulai</label>
              <input type="date" class="form-control" id="startDatePembelian" name="start_date" required>
            </div>
            <div class="col-md-6">
              <label for="endDatePembelian" class="form-label">Tanggal Selesai</label>
              <input type="date" class="form-control" id="endDatePembelian" name="end_date" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat Laporan PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
