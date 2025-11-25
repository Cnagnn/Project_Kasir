

@extends('layouts.admin')

@section('content')

    <div class="container mt-2">
        <h2 class="mb-4 text-primary fw-bold">Riwayat Penjualan</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Filter Tanggal
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="tanggal_mulai" class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="tanggal_akhir" class="form-label fw-bold">Tanggal Berakhir</label>
                            <input type="date" class="form-control" name="tanggal_akhir" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100" id="btnSearch">
                                <i class="bi bi-search"></i> Tampilkan Riwayat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm animate__animated animate__fadeIn" id="resultCard" style="display: none;">
            <div class="card-header bg-primary text-white fw-bold">
                Hasil Pencarian
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Invoice</th>
                                <th>Operator</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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
                            // Loop data transaksi
                            $.each(response.data, function(index, item) {
                                // Format Rupiah sederhana
                                let formattedPrice = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(item.total_payment);

                                // Format Tanggal (Opsional, sesuaikan kebutuhan format)
                                let dateObj = new Date(item.created_at);
                                let formattedDate = dateObj.toLocaleDateString('id-ID') + ' ' + dateObj.toLocaleTimeString('id-ID');

                                let detailUrl = `{{ url('/transaction_history/detail') }}/${item.id}`;
                                let row = `
                                    <tr>
                                        <td>${formattedDate}</td>
                                        <td>${item.invoice_number}</td>
                                        <td>${item.user ? item.user.name : '-'}</td>
                                        <td>${formattedPrice}</td>
                                        <td><a href="${detailUrl}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i></a></td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                        } else {
                            // Jika data kosong
                            tbody.append(`
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Tidak ada data transaksi pada rentang tanggal ini.
                                    </td>
                                </tr>
                            `);
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
        });
        </script>
@endpush
