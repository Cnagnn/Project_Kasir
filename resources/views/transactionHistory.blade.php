

@extends('layouts.admin')

@section('page-title', 'Riwayat Transaksi')
@section('page-description', 'Lihat histori transaksi penjualan')

@section('content')

<style>
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
    .action-btn-group {
        display: inline-flex;
        align-items: stretch;
        border-radius: 999px;
        overflow: hidden;
        background: var(--bs-primary);
    }
    .action-btn-group .btn {
        border: none;
        border-radius: 0;
        background: transparent;
        color: #fff;
        padding: 0.45rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .action-btn-group .btn + .btn {
        border-left: 1px solid rgba(255, 255, 255, 0.2);
    }
    .action-btn-group .btn:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
    }
    .action-btn-group a {
        border: none;
        border-radius: 0;
        background: transparent;
        color: #fff;
        padding: 0.45rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .action-btn-group a + .btn,
    .action-btn-group .btn + a {
        border-left: 1px solid rgba(255, 255, 255, 0.2);
    }
    .action-btn-group a:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
    }
</style>

<div class="row">
    <div class="col-sm-12" style="padding-left: 0; padding-right: 0;">

        {{-- MAIN CONTENT CONTAINER --}}
        <div id="mainContentContainer">

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card card-rounded">
            <div class="card-body">
                <h4 class="card-title mb-4">Filter Tanggal</h4>
                <form id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" required>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="tanggal_akhir" class="form-label">Tanggal Berakhir</label>
                            <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100" id="btnSearch">
                                <i class="mdi mdi-magnify"></i> Tampilkan Riwayat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card card-rounded" id="resultCard" style="display: none;">
            <div class="card-body">
                <h4 class="card-title mb-4">Hasil Pencarian</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>Tanggal</th>
                                <th>No. Invoice</th>
                                <th>Operator</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-center" id="totalPendapatan">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        </div>

        </div>
        {{-- END MAIN CONTENT CONTAINER --}}

        {{-- DETAIL CONTENT CONTAINER (Hidden by default) --}}
        <div id="detailContentContainer" style="display: none;">
            {{-- Detail content will be loaded here via AJAX --}}
        </div>
        {{-- END DETAIL CONTENT CONTAINER --}}

    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Set format date input untuk browser yang mendukung
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                // Tambahkan atribut untuk format dd/mm/yyyy jika browser support
                input.setAttribute('pattern', '\\d{2}/\\d{2}/\\d{4}');
            });
            
            $('#filterForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah reload halaman

                // Ambil URL dari route Laravel
                let url = "{{ route('transactionHistory.getTransactionHistory') }}";
                
                // Ambil data dari form
                let formData = $(this).serialize();

                // Tampilkan loading (opsional, UX yang baik)
                let btn = $('#btnSearch');
                let originalText = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm"></span> Loading...').prop('disabled', true);

                $.ajax({
                    url: url,
                    type: "GET",
                    data: formData,
                    dataType: "json", // Mengharapkan balasan JSON
                    success: function(response) {
                        // console.log(response.data);
                        
                        let tbody = $('#tableBody');
                        tbody.empty(); // Kosongkan tabel lama

                        // Tampilkan card hasil
                        $('#resultCard').show();

                        if (response.data.length > 0) {
                            let totalPendapatan = 0;
                            
                            // Loop data transaksi
                            $.each(response.data, function(index, item) {
                                // Hitung total pendapatan
                                totalPendapatan += parseFloat(item.total_payment);
                                
                                // Format Rupiah sederhana
                                let formattedPrice = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(item.total_payment);

                                // Format Tanggal (Opsional, sesuaikan kebutuhan format)
                                let dateObj = new Date(item.created_at);
                                let day = String(dateObj.getDate()).padStart(2, '0');
                                let month = String(dateObj.getMonth() + 1).padStart(2, '0');
                                let year = dateObj.getFullYear();
                                let hours = String(dateObj.getHours()).padStart(2, '0');
                                let minutes = String(dateObj.getMinutes()).padStart(2, '0');
                                let seconds = String(dateObj.getSeconds()).padStart(2, '0');
                                let formattedDate = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

                                let detailUrl = `{{ url('/transaction_history/detail') }}/${item.id}`;
                                let row = `
                                    <tr class="text-center">
                                        <td>${formattedDate}</td>
                                        <td>${item.invoice_number}</td>
                                        <td>${item.user ? item.user.name : '-'}</td>
                                        <td>${formattedPrice}</td>
                                        <td>
                                                <div class="action-btn-group" role="group" aria-label="Aksi">
                                                    <button class="btn btn-primary btn-sm btn-view-detail" data-transaction-id="${item.id}" title="Edit / Detail"><i class="mdi mdi-pencil"></i></button>
                                                    <button type="button" class="btn btn-primary btn-sm btn-print-invoice" data-transaction-id="${item.id}" title="Print Ulang"><i class="mdi mdi-printer"></i></button>
                                                </div>
                                        </td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                            
                            // Update total pendapatan di footer
                            let formattedTotal = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(totalPendapatan);
                            $('#totalPendapatan').text(formattedTotal);
                            
                        } else {
                            // Jika data kosong
                            tbody.append(`
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Tidak ada data transaksi pada rentang tanggal ini.
                                    </td>
                                </tr>
                            `);
                            // Reset total
                            $('#totalPendapatan').text('Rp 0');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert("Terjadi kesalahan saat mengambil data.");
                    },
                    complete: function() {
                        // Kembalikan tombol ke semula
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });
            // Handler print ulang struk
            $(document).on('click', '.btn-print-invoice', function() {
                const id = $(this).data('transaction-id');
                if(!id) return;
                // Asumsi sudah ada endpoint yang mengembalikan HTML siap print
                // Jika endpoint JSON, logic parsing perlu ditambahkan terpisah.
                const printUrl = `/transaction_history/print/${id}`; // Route baru untuk cetak struk
                const w = window.open(printUrl, '_blank');
                if(!w) {
                    alert('Popup terblokir. Izinkan popup untuk mencetak struk.');
                    return;
                }
                // Halaman print sudah memiliki auto-print script, jadi tidak perlu trigger print() lagi di sini
            });

            // === HANDLE VIEW DETAIL BUTTON ===
            $(document).on('click', '.btn-view-detail', function(e) {
                e.preventDefault();
                const transactionId = $(this).data('transaction-id');
                
                // Show loading state
                $('#detailContentContainer').html('<div class="text-center p-5"><i class="mdi mdi-loading mdi-spin" style="font-size: 48px;"></i><p>Memuat detail...</p></div>');
                $('#detailContentContainer').show();
                $('#mainContentContainer').hide();
                
                // Fetch detail content via AJAX
                $.ajax({
                    url: `/transaction_history/detail/${transactionId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#detailContentContainer').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading detail:', error);
                        $('#detailContentContainer').html(`
                            <div class="alert alert-danger m-3">
                                <h4>Error</h4>
                                <p>Gagal memuat detail transaksi. Silakan coba lagi.</p>
                                <button class="btn btn-primary btn-back-to-history">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </button>
                            </div>
                        `);
                    }
                });
            });

            // === HANDLE BACK TO LIST BUTTON ===
            $(document).on('click', '.btn-back-to-history', function(e) {
                e.preventDefault();
                $('#detailContentContainer').hide().html('');
                $('#mainContentContainer').show();
            });
        });
        </script>
@endpush
