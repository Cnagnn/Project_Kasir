@extends('layouts.admin') 

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="page-title">Edit Produk: {{ $product->name }}</h4>
        </div>
    </div>

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
   
    
    <div class="col">
        {{-- BAGIAN 1: FORM EDIT PRODUK UTAMA --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Utama Produk</h5>
                    <hr>
                    {{-- Form ini akan mengarah ke ProductController@update --}}
                    <form action="{{ route('product.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <input type="hidden" name="id" value="{{ $product->id }}">
                        </div>

                        <div class="mb-3">
                            <label for="product_name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="product_name" name="product_name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Kategori</label>
                            
                            @php
                                // Logika untuk menentukan ID & Nama kategori yang dipilih saat ini
                                // Prioritas: 1. Data lama (jika validasi error), 2. Data produk, 3. Kosong
                                $selectedId = old('category_id', $product->category_id);
                                $selectedCategory = $categories->firstWhere('id', $selectedId);
                                $selectedName = $selectedCategory ? $selectedCategory->name : '-- Pilih Kategori --';
                            @endphp

                            {{-- Input hidden untuk menyimpan ID kategori --}}
                            <input type="hidden" name="category_id" id="selected_category_id" 
                                value="{{ $selectedId }}" required>

                            <div class="btn-group d-block">
                                {{-- Tombol untuk menampilkan nama kategori terpilih --}}
                                <button type="button" class="btn btn-outline-primary" id="category_dropdown_button">
                                    {{ $selectedName }}
                                </button>
                                {{-- Tombol panah dropdown --}}
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
                                        data-toggle="dropdown" aria-expanded="false"> {{-- <== UBAH DI SINI --}}
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                
                                {{-- Daftar pilihan kategori --}}
                                <ul class="dropdown-menu" id="category_options">
                                    @foreach ($categories as $category)
                                        <li>
                                            <a class="dropdown-item {{ $selectedId == $category->id ? 'active' : '' }}" 
                                            href="#" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            @error('category_id')
                                {{-- Menampilkan error validasi untuk kategori --}}
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Data Produk</button>
                        <a href="{{ route('product.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>


                    {{-- TABEL BATCH PRODUK --}}

                    <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top: 10%">
                        <h5 class="card-title m-0">Daftar Batch Stok</h5>
                        {{-- TOMBOL UNTUK MODAL "TAMBAH BATCH BARU" --}}
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addBatchModal">
                            <i class="mdi mdi-plus"></i> Tambah Batch Baru
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Batch ID</th>
                                    <th>Tgl. Masuk</th> {{-- Asumsi dari created_at --}}
                                    <th>Stok Awal</th> {{-- Asumsi kolom 'initial_stock' --}}
                                    <th>Stok Tersisa</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($product->stockBatches as $batch)
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
                                        <td>
                                            {{-- TOMBOL INI AKAN MEMBUKA MODAL EDIT BATCH --}}
                                            <button type="button" class="btn btn-warning btn-sm btn-edit-batch" 
                                                    data-toggle="modal" 
                                                    data-target="#editBatchModal"
                                                    data-batch-id="{{ $batch->id }}"
                                                    data-initial-stock="{{ $batch->initial_stock ?? 0 }}"
                                                    data-remaining-stock="{{ $batch->remaining_stock }}"
                                                    data-buy-price="{{ $batch->buy_price }}"
                                                    data-sell-price="{{ $batch->sell_price }}"
                                                    data-update-url="{{ route('stock_batches.update', $batch->id) }}"
                                                    > 
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            
                                            {{-- Form delete batch (jika diperlukan) --}}
                                            <form 
                                                action="{{ route('stock_batches.destroy', $batch->id) }}" 
                                                method="POST" class="form-delete d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
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
    </div>
</div>


{{-- MODAL TAMBAH BATCH --}}
<div class="modal fade" id="addBatchModal" tabindex="-1" role="dialog" aria-labelledby="addBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Pastikan route 'stock_batches.store' sudah ada di web.php --}}
            <form action="{{ route('stock_batches.store') }}" method="POST">
                @csrf
                {{-- $product tersedia karena kita berada di edit.blade.php --}}
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="addBatchModalLabel">Tambah Batch Baru</h5>
                    {{-- Sesuaikan tombol close ini dengan versi Bootstrap Anda --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="initial_stock" class="form-label">Stok Awal</label>
                        <input type="number" class="form-control" id="initial_stock" oninput="StockInput()" name="initial_stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="remaining_stock" class="form-label">Stok Tersisa</label>
                        <input type="number" class="form-control" id="remaining_stock" name="remaining_stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="buy_price" class="form-label">Harga Beli</label>
                        <input type="number" class="form-control" name="buy_price" required>
                    </div>
                    <div class="mb-3">
                        <label for="sell_price" class="form-label">Harga Jual</label>
                        <input type="number" class="form-control" name="sell_price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Batch</button>
                </div>
            </form>
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

@endsection



@push('scripts')
<script>
    // Cukup satu $(document).ready() untuk semua skrip
    $(document).ready(function() {
        
        // =============================================
        // Script untuk custom category dropdown
        // =============================================
        $('#category_options .dropdown-item').on('click', function(event) {
            event.preventDefault(); // Mencegah link # beraksi
            var selectedId = $(this).data('id');
            var selectedName = $(this).data('name');
            $('#selected_category_id').val(selectedId);
            $('#category_dropdown_button').text(selectedName);
            $('#category_options .dropdown-item').removeClass('active');
            $(this).addClass('active');
        });

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


    // ISI STOCK OTOMATIS UNTUK FORM REMAINING STOCK BERDASARKAN INITISD
    function StockInput() {
        var nilaiSumber = document.getElementById('initial_stock').value;
        document.getElementById('remaining_stock').value = nilaiSumber;
    }
</script>
@endpush