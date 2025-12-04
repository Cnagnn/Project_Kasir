@extends('layouts.admin')

@section('page-title', 'Detail Transaksi')
@section('page-description', 'Lihat dan edit detail transaksi')

@section('content')

@if(session()->has('success'))
    <script>
        Swal.fire({
            title: "BERHASIL",
            text: "{{ session('success') }}",
            icon: "success"
        }).then((result) => {
            @if(session('show_receipt'))
                window.open('/transaction_history/print/{{ session('transaction_id') }}', '_blank');
            @endif
        });
    </script>    
@endif

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
    .action-btn-group form {
        margin: 0;
        display: inline-flex;
    }
    .table-centered th,
    .table-centered td {
        text-align: center;
        vertical-align: middle;
    }
    .dropdown-list-container {
        max-height: 200px;
        overflow-y: auto;
    }
    .dropdown-menu {
        padding: 0;
    }
    .dropdown-search {
        padding: 0.5rem 0.75rem;
    }
</style>

<div class="row">
    <div class="col-sm-12">

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-4">Info Transaksi</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">No. Invoice</label>
                        <input type="text" class="form-control" id="invoice_number" value="{{ $transaction->invoice_number }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                        <input type="text" class="form-control" id="transaction_date" value="{{ $transaction->created_at->format('d M Y H:i') }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="operator" class="form-label">Operator</label>
                        <input type="text" class="form-control" id="operator" value="{{ $transaction->user->name ?? '-' }}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <input type="text" class="form-control" id="payment_method" value="{{ ucfirst($transaction->payment_method) }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="total_payment" class="form-label">Total Pembayaran</label>
                        <input type="text" class="form-control" id="total_payment" value="Rp {{ number_format($transaction->total_payment, 0, ',', '.') }}" disabled>
                    </div>
                    @if($transaction->payment_method === 'cash')
                    <div class="mb-3">
                        <label for="change_amount" class="form-label">Kembalian</label>
                        <input type="text" class="form-control" id="change_amount" value="Rp {{ number_format($transaction->change_amount ?? 0, 0, ',', '.') }}" disabled>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

        {{-- TABEL DETAIL TRANSAKSI --}}
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card card-rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Detail Item Transaksi</h4>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-centered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th>Harga Jual</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaction->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->product->name ?? 'Produk Terhapus' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>Rp {{ number_format($detail->product_sell_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td>
                                    <div class="action-btn-group" role="group" aria-label="Aksi detail">
                                        <button type="button" class="btn btn-primary btn-sm btn-edit-detail" 
                                                data-toggle="modal" 
                                                data-target="#editDetailModal"
                                                data-detail-id="{{ $detail->id }}"
                                                data-product-id="{{ $detail->product_id }}"
                                                data-product-name="{{ $detail->product->name ?? 'Produk Terhapus' }}"
                                                data-quantity="{{ $detail->quantity }}"
                                                data-buy-price="{{ $detail->product_buy_price }}"
                                                data-sell-price="{{ $detail->product_sell_price }}"> 
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <form action="" method="POST" class="form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada detail item.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th colspan="4" class="text-end">Total:</th>
                            <th>Rp {{ number_format($transaction->details->sum('subtotal'), 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

        <div class="col-lg-12 mb-3">
            <a href="{{ route('transactionHistory.index') }}" class="btn btn-primary">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>

{{-- MODAL EDIT DETAIL TRANSAKSI --}}
<div class="modal fade" id="editDetailModal" tabindex="-1" role="dialog" aria-labelledby="editDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" id="editDetailForm"> 
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editDetailModalLabel">Edit Detail Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_product_name" class="form-label">Nama Produk</label>
                        <div class="dropdown w-100">
                            <input type="hidden" id="edit_product_id" name="product_id">
                            <input type="text" id="edit_product_name" class="form-control" placeholder="Klik untuk memilih produk..." readonly autocomplete="off" style="background-color: #fff; cursor: pointer;">
                            <div class="dropdown-menu w-100">
                                <input type="text" class="form-control dropdown-search" placeholder="Cari produk...">
                                <div class="dropdown-list-container">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" required>
                    </div>
                    <input type="hidden" id="edit_buy_price" name="product_buy_price">
                    <div class="mb-3">
                        <label for="edit_sell_price" class="form-label">Harga Jual</label>
                        <input type="number" class="form-control" id="edit_sell_price" name="product_sell_price" required readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Detail</button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Fungsi fetch products
        function fetchProducts(query, dropdownMenu) {
            const listContainer = dropdownMenu.find('.dropdown-list-container');
            listContainer.html('<div class="dropdown-item">Memuat...</div>');

            $.ajax({
                url: "{{ route('purchasing.products.search') }}",
                type: "GET",
                data: { 'query': query },
                success: function(data) {
                    listContainer.html('');
                    if (data.length > 0) {
                        $.each(data, function(index, product) {
                            const availableStock = product.stock.find(s => s.remaining_stock > 0);
                            const sellPrice = availableStock ? availableStock.sell_price : 0;
                            const buyPrice = availableStock ? availableStock.buy_price : 0;
                            
                            listContainer.append(
                                `<button type="button" class="dropdown-item" 
                                    data-id="${product.id}" 
                                    data-name="${product.name}" 
                                    data-sellprice="${sellPrice}"
                                    data-buyprice="${buyPrice}">${product.name}</button>`
                            );
                        });
                    } else {
                        listContainer.html('<div class="dropdown-item text-muted">Produk tidak ditemukan.</div>');
                    }
                }
            });
        }

        // Handler untuk button edit detail
        $('.btn-edit-detail').on('click', function() {
            var button = $(this);
            
            var detailId = button.data('detail-id');
            var productId = button.data('product-id');
            var productName = button.data('product-name');
            var quantity = button.data('quantity');
            var buyPrice = button.data('buy-price');
            var sellPrice = button.data('sell-price');
            
            var modal = $('#editDetailModal');
            
            // Set form action URL
            var updateUrl = "{{ url('/transaction_history/detail/update') }}/" + detailId;
            modal.find('form').attr('action', updateUrl);
            
            // Set product ID (penting untuk validasi)
            modal.find('#edit_product_id').val(productId);
            modal.find('#edit_product_name').val(productName);
            modal.find('#edit_quantity').val(quantity);
            modal.find('#edit_buy_price').val(buyPrice);
            modal.find('#edit_sell_price').val(sellPrice);
        });

        // Dropdown product search handlers
        $('#editDetailModal').on('click', '#edit_product_name', function() {
            const dropdownMenu = $(this).siblings('.dropdown-menu');
            dropdownMenu.addClass('show');
            $(this).attr('aria-expanded', 'true');
            fetchProducts("", dropdownMenu);
            setTimeout(() => {
                dropdownMenu.find('.dropdown-search').focus();
            }, 100);
        });

        $('#editDetailModal').on('keyup', '.dropdown-search', function() {
            const query = $(this).val();
            const dropdownMenu = $(this).closest('.dropdown-menu');
            fetchProducts(query, dropdownMenu);
        });

        $('#editDetailModal').on('click', '.dropdown-item', function(e) {
            e.preventDefault();
            let productId = $(this).data('id');
            let productName = $(this).data('name');
            let sellPrice = $(this).data('sellprice');
            let buyPrice = $(this).data('buyprice');

            if (!productId) return;

            $('#edit_product_id').val(productId);
            $('#edit_product_name').val(productName);
            $('#edit_sell_price').val(sellPrice);
            $('#edit_buy_price').val(buyPrice);

            const dropdownMenu = $(this).closest('.dropdown-menu');
            dropdownMenu.removeClass('show');
            $('#edit_product_name').attr('aria-expanded', 'false');
        });

        $('#editDetailModal').on('click', '.dropdown-search', function(e) {
            e.stopPropagation();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#editDetailModal .dropdown').length) {
                $('#editDetailModal .dropdown-menu').removeClass('show');
                $('#edit_product_name').attr('aria-expanded', 'false');
            }
        });
    });
</script>
@endpush
