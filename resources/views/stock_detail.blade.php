@extends('layouts.admin') 

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
</style>

@if(session()->has('product_edit_success'))
        <script>
            Swal.fire({
                title: "BERHASIL",
                text: "{{ session('edit_product_success') }}",
                icon: "success"
            });
        </script>    
    @endif
    @if(session()->has('batch_add_success'))
        <script>
            Swal.fire({
                title: "BERHASIL",
                text: "{{ session('batch_add_success') }}",
                icon: "success"
            });
        </script>    
    @endif

    <style>
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
    </style>
    @if(session()->has('batch_update_success'))
        <script>
            Swal.fire({
                title: "BERHASIL",
                text: "{{ session('batch_update_success') }}",
                icon: "success"
            });
        </script>    
    @endif
    @if(session()->has('batch_destroy_success'))
        <script>
            Swal.fire({
                title: "BERHASIL",
                text: "{{ session('batch_destroy_success') }}",
                icon: "success"
            });
        </script>    
    @endif
   
<div class="row">
    <div class="col-sm-12">
    
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card card-rounded">
            <div class="card-body">
                <h4 class="card-title mb-4">Info Produk</h4>
                <div class="mb-3">
                    <label for="product_name" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        id="product_name" name="product_name" value="{{ old('name', $product->name) }}" disabled>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_name" class="form-label">Kategori Produk</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        id="category_name" name="category_name" value="{{ old('name', $product->category->name) }}" disabled>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        </div>

        {{-- TABEL BATCH PRODUK --}}
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card card-rounded">
            <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Batch Produk</h4>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-centered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Batch ID</th>
                                    <th>Tgl. Masuk</th> {{-- Asumsi dari created_at --}}
                                    <th>Stok Awal</th> {{-- Asumsi kolom 'initial_stock' --}}
                                    <th>Stok Tersisa</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    @if (Auth::user()->role->name != "Cashier")
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($product->stock as $batch)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- Ganti 'created_at' jika Anda punya kolom tgl masuk yg berbeda --}}
                                        <td>{{ $batch->id }}</td>
                                        <td>{{ $batch->created_at->format('d M Y') }}</td> 
                                        {{-- Ganti 'initial_stock' jika nama kolomnya berbeda --}}
                                        <td>{{ $batch->initial_stock ?? 'N/A' }}</td> 
                                        <td>{{ $batch->remaining_stock }}</td>
                                        <td>Rp {{ number_format($batch->buy_price, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($batch->sell_price, 0, ',', '.') }}</td>
                                        @if (Auth::user()->role->name != "Cashier")
                                            <td>
                                                <div class="action-btn-group" role="group" aria-label="Aksi batch">
                                                    <button type="button" class="btn btn-primary btn-sm btn-edit-batch" 
                                                            data-toggle="modal" 
                                                            data-target="#editBatchModal"
                                                            data-batch-id="{{ $batch->id }}"
                                                            data-initial-stock="{{ $batch->initial_stock ?? 0 }}"
                                                            data-remaining-stock="{{ $batch->remaining_stock }}"
                                                            data-buy-price="{{ $batch->buy_price }}"
                                                            data-sell-price="{{ $batch->sell_price }}"
                                                            data-update-url="{{ route('stock.update', $batch->id) }}"
                                                            > 
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    
                                                    {{-- Form delete batch (jika diperlukan) --}}
                                                    <form 
                                                        action="{{ route('stock_batches.destroy', $batch->id) }}" 
                                                        method="POST" class="form-delete d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data batch stok untuk produk ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>

{{-- MODAL EDIT BATCH --}}
<div class="modal fade" id="editBatchModal" tabindex="-1" role="dialog" aria-labelledby="editBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Action form akan di-set oleh JavaScript --}}
            <form action="" method="POST"> 
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBatchModalLabel">Edit Data Batch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_initial_stock" class="form-label">Stok Awal</label>
                        <input type="number" class="form-control" id="edit_initial_stock" name="initial_stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_remaining_stock" class="form-label">Stok Tersisa</label>
                        <input type="number" class="form-control" id="edit_remaining_stock" name="remaining_stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_buy_price" class="form-label">Harga Beli</label>
                        <input type="number" class="form-control" id="edit_buy_price" name="buy_price" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sell_price" class="form-label">Harga Jual</label>
                        <input type="number" class="form-control" id="edit_sell_price" name="sell_price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Batch</button>
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
    // Cukup satu $(document).ready() untuk semua skrip
    $(document).ready(function() {

        // =============================================
        // Script untuk mengisi data ke MODAL EDIT BATCH
        // =============================================
        $('.btn-edit-batch').on('click', function() {
            var button = $(this);
            
            // Ambil data dari atribut data-*
            var batchId = button.data('batch-id');
            var initialStock = button.data('initial-stock');
            var remainingStock = button.data('remaining-stock');
            var buyPrice = button.data('buy-price');
            var sellPrice = button.data('sell-price');
            
            // AMBIL URL DARI 'data-update-url' YANG SUDAH AKTIF
            var updateUrl = button.data('update-url'); 
            
            var modal = $('#editBatchModal');
            
            // Set action form di dalam modal
            modal.find('form').attr('action', updateUrl);
            
            // Isi nilai form di dalam modal
            modal.find('#edit_initial_stock').val(initialStock);
            modal.find('#edit_remaining_stock').val(remainingStock);
            modal.find('#edit_buy_price').val(buyPrice);
            modal.find('#edit_sell_price').val(sellPrice);
        });
    });

    document.addEventListener('DOMContentLoaded', function (){
        const deleteForms = document.querySelectorAll('.form-delete');

        deleteForms.forEach(form=>{
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const button = form.querySelector('button[type="submit"]');
                const productName = document.querySelector('#product_name');

                const productNameValue = productName.value;

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan menghapus "${productNameValue}".`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    // 3. JIKA PENGGUNA KLIK "YA"
                    if (result.isConfirmed) {
                        // Lanjutkan proses submit form (SEKARANG HALAMAN AKAN RELOAD)
                        form.submit(); 
                    }
                });
                
            })
        })
    })
</script>
@endpush