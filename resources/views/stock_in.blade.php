@extends('layouts.admin')

@section('content')

     @if(session()->has('success'))
        <script>
            Swal.fire({
                title: "BERHASIL",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>    
    @endif

    <div class="container mt-5">
        <h2>Form Penambahan Stok</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('purchasing.stock.in.process') }}" method="POST">
            @csrf
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th style="width: 40%;">Nama Produk</th>
                        <th style="width: 20%;">Kategori</th>
                        <th style="width: 15%;">Kuantitas</th>
                        <th style="width: 20%;">Harga Beli</th>
                        <th style="width: 5%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="stock-table-body">
                </tbody>
            </table>

            <button type="button" class="btn btn-primary mt-3" id="add-row-btn"> + Tambah Baris</button>
            <button type="submit" class="btn btn-success float-end mt-3">Simpan ke Database</button>
        </form>
    </div>
@endsection

@push('scripts')

{{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            
            // Fungsi untuk menambahkan baris baru
            function addNewRow() {
                const rowHtml = `
                    <tr>
                        <td>
                            <div class="dropdown">
                                <input type="hidden" name="product_id[]" class="product-id">
                                <input type="text" name="product_name[]" class="form-control product-search" placeholder="Ketik nama produk..." 
                                       data-bs-toggle="dropdown" autocomplete="off">
                                <div class="dropdown-menu w-100">
                                    </div>
                            </div>
                        </td>
                        <td><input type="text" class="form-control category-name" readonly></td>
                        <td><input type="number" name="quantity[]" class="form-control qty-input" min="1" required></td>
                        <td><input type="number" name="purchase_price[]" class="form-control price-input" min="0" step="any" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row-btn">X</button></td>
                    </tr>
                `;
                $('#stock-table-body').append(rowHtml);
            }

            // Tambahkan baris pertama saat halaman dimuat
            addNewRow();

            // Event handler untuk tombol "Tambah Baris"
            $('#add-row-btn').on('click', function() {
                addNewRow();
            });

            // Event handler untuk menghapus baris (menggunakan event delegation)
            $('#stock-table-body').on('click', '.remove-row-btn', function() {
                // Jangan hapus jika hanya ada satu baris
                if ($('#stock-table-body tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    Swal.fire({
                        title: "GAGAL",
                        text: "Minimal Harus Ada 1 Baris!",
                        icon: "error"
                    });
                }
            });

            // --- LOGIKA LIVE SEARCH (MENGGUNAKAN EVENT DELEGATION) ---
            let currentSearchInput;

            // Saat pengguna mengetik di salah satu input produk
 
            $('#stock-table-body').on('keyup', '.product-search', function() {
                let currentSearchInput = $(this); // Didefinisikan di sini agar scope-nya jelas
                let query = $(this).val();
                let dropdownMenu = currentSearchInput.siblings('.dropdown-menu');

                if(query == ""){
                    let currentRow = $(this).closest('tr');
                    currentRow.find('.category-name').val("");
                    currentRow.find('.qty-input').val("");
                    currentRow.find('.price-input').val("");
                    // console.log(currentRow.find('.category-name'));
                    
                }

                if (query.length > 1) {
                    $.ajax({
                        // Pastikan nama route Anda benar
                        url: "{{ route('purchasing.products.search') }}", 
                        type: "GET",
                        data: { 'query': query },
                        success: function(data) {
                            dropdownMenu.html(''); 
                            if (data.length > 0) {
                                $.each(data, function(index, product) {
                                    // PENTING: Gunakan <button> dengan class "dropdown-item"
                                    dropdownMenu.append(
                                        `<button type="button" class="dropdown-item" 
                                            data-id="${product.id}" 
                                            data-name="${product.name}" 
                                            data-category="${product.category.name}">${product.name}</button>`
                                    );
                                });
                                dropdownMenu.addClass('show');
                                currentSearchInput.attr('aria-expanded', 'true');
                            } else {
                                dropdownMenu.removeClass('show');
                                currentSearchInput.attr('aria-expanded', 'false');
                            }
                        }
                    });
                } else {
                    // Sembunyikan dropdown dengan menghapus class .show
                    dropdownMenu.removeClass('show');
                    currentSearchInput.attr('aria-expanded', 'false');
                }
            });

            // 'click' handler yang sudah diperbaiki
            $('#stock-table-body').on('click', '.dropdown-item', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let productId = $(this).data('id');
                let productName = $(this).data('name');
                let categoryName = $(this).data('category');
                
                // PENTING: Cari baris berdasarkan item yang diklik (this), bukan variabel global
                let currentRow = $(this).closest('tr');
                currentRow.find('.product-id').val(productId);
                currentRow.find('.product-search').val(productName);
                currentRow.find('.category-name').val(categoryName);

                // PERUBAHAN 2: Sembunyikan dropdown dengan menghapus class .show
                let dropdownMenu = $(this).closest('.dropdown-menu');
                dropdownMenu.removeClass('show');
                // Juga update atribut pada inputnya
                let currentSearchInput = dropdownMenu.siblings('.product-search');
                currentSearchInput.attr('aria-expanded', 'false');
            });

            $(document).on('click', function(e) {
                // Jika elemen yang diklik BUKAN bagian dari komponen dropdown (.dropdown)
                if (!$(e.target).closest('.dropdown').length) {
                    // Sembunyikan SEMUA dropdown menu yang mungkin sedang terbuka
                    $('.dropdown-menu').removeClass('show');
                    $('.product-search').attr('aria-expanded', 'false');
                }
            });
        });
    </script>
@endpush
    
