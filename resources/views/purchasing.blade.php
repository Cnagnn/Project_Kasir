@extends('layouts.admin')

@section('page-title', 'Pembelian')
@section('page-description', 'Catat pembelian barang dan kelola stok masuk')

@section('content')
    
    <style>
        /* Mengatur agar daftar produk bisa di-scroll, terpisah dari search bar */
        .dropdown-list-container {
            max-height: 200px; /* Atur tinggi maksimal daftar */
            overflow-y: auto;  /* Tambahkan scrollbar jika perlu */
        }
        .dropdown-menu {
            /* Hapus padding default agar search bar menempel */
            padding: 0; 
        }
    </style>

     @if(session()->has('success'))
        <script>
            Swal.fire({
                title: "BERHASIL",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>    
    @endif

    <div class="container mt-2">
        <h2 class="mb-4 text-primary fw-bold">Purchasing Form</h2>

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
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th style="width: 30%;">Nama Produk</th>
                        <th style="width: 20%;">Kategori</th>
                        <th style="width: 15%;">Kuantitas</th>
                        <th style="width: 15%;">Harga Beli</th>
                        <th style="width: 15%;">Harga Jual</th>
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
<style>
    /* Mengatur agar daftar produk bisa di-scroll, terpisah dari search bar */
    .dropdown-list-container {
        max-height: 200px; /* Atur tinggi maksimal daftar */
        overflow-y: auto;  /* Tambahkan scrollbar jika perlu */
    }
    .dropdown-menu {
        padding: 0; 
    }
    /* Beri padding pada search bar internal */
    .dropdown-search {
        padding: 0.5rem 0.75rem;
    }
</style>

<script>
    $(document).ready(function() {
        
        // --- FUNGSI BARU UNTUK MENGAMBIL PRODUK ---
        // Dibuat reusable agar bisa dipanggil saat dropdown dibuka dan saat mencari
        function fetchProducts(query, dropdownMenu) {
            const listContainer = dropdownMenu.find('.dropdown-list-container');
            listContainer.html('<div class="dropdown-item">Memuat...</div>'); // Tampilkan loading

            $.ajax({
                url: "{{ route('purchasing.products.search') }}", // Sesuaikan dengan route Anda
                type: "GET",
                data: { 'query': query },
                success: function(data) {
                    console.log(data);
                    
                    listContainer.html(''); // Kosongkan daftar
                    if (data.length > 0) {
                        $.each(data, function(index, product) {
                            const availableStock = product.stock.find(s => s.remaining_stock > 0);
                            const sellPrice = availableStock ? availableStock.sell_price : 0;
                            // console.log(sellPrice);
                            
                            listContainer.append(
                                `<button type="button" class="dropdown-item" 
                                    data-id="${product.id}" 
                                    data-name="${product.name}" 
                                    data-category="${product.category.name}"
                                    data-sellprice="${sellPrice}">${product.name}</button> `
                            );
                        });
                    } else {
                        listContainer.html('<div class="dropdown-item text-muted">Produk tidak ditemukan.</div>');
                    }
                }
            });
        }

        // --- FUNGSI UNTUK MENAMBAH BARIS BARU ---
        function addNewRow() {
            // PERUBAHAN BESAR PADA STRUKTUR HTML:
            const rowHtml = `
                <tr>
                    <td>
                        <div class="input-group">
                            <div class="dropdown flex-grow-1">
                                <input type="hidden" name="product_id[]" class="product-id">
                                
                                <input type="text" name="product_name[]" class="form-control product-search" placeholder="Klik untuk memilih produk..." 
                                       readonly autocomplete="off" style="background-color: #fff;">
                                
                                <div class="dropdown-menu w-100">
                                    <input type="text" class="form-control dropdown-search" placeholder="Cari produk...">
                                    <div class="dropdown-list-container">
                                        </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><input type="text" class="form-control category-name" readonly></td>
                    <td><input type.number" name="quantity[]" class="form-control" min="1" required></td>
                    <td><input type="number" name="buy_price[]" class="form-control" min="0" step="any" required></td>
                    <td><input type="number" name="sell_price[]" class="form-control sell-price" min="0" step="any" required></td>
                    <td><button type="button" class="btn btn-outline-danger mdi mdi-delete-outline remove-row-btn" title="Hapus Baris"><i class="bi bi-trash"></i></button></td>
                </tr>
            `;
            $('#stock-table-body').append(rowHtml);
        }

        addNewRow();

        $('#add-row-btn').on('click', function() {
            addNewRow();
        });

        $('#stock-table-body').on('click', '.remove-row-btn', function() {
            const $row = $(this).closest('tr');

            const $productNameInput = $row.find('td:first-child input');

            if ($productNameInput.length > 0 && $productNameInput.val().trim() !== '') {
                
                $row.find('input, select').val('');

                return; 
            }
            else if ($('#stock-table-body tr').length > 1) {
                $(this).closest('tr').remove();
            } 
            else {
                Swal.fire({
                    text: "Minimal harus ada 1 baris !",
                    icon: "error"
                });
            }
        });

        // ==========================================================
        // LOGIKA BARU UNTUK DROPDOWN
        // ==========================================================

        // 1. Saat input utama (readonly) diklik
        $('#stock-table-body').on('click', '.product-search', function() {
            const dropdownMenu = $(this).siblings('.dropdown-menu');
            
            // Tampilkan dropdown
            dropdownMenu.addClass('show');
            $(this).attr('aria-expanded', 'true');

            // Ambil daftar produk awal (query kosong)
            fetchProducts("", dropdownMenu);
            
            // Fokus ke search bar di dalam dropdown
            setTimeout(() => {
                dropdownMenu.find('.dropdown-search').focus();
            }, 100);
        });

        // 2. Saat mengetik di search bar INTERNAL
        $('#stock-table-body').on('keyup', '.dropdown-search', function() {
            const query = $(this).val();
            const dropdownMenu = $(this).closest('.dropdown-menu');
            fetchProducts(query, dropdownMenu);
        });

        // 3. Saat mengklik item produk
        $('#stock-table-body').on('click', '.dropdown-item', function(e) {
            e.preventDefault();

            let productId = $(this).data('id');
            let productName = $(this).data('name');
            let categoryName = $(this).data('category');
            let sellPrice = $(this).data('sellprice');
            console.log("ini adalah harga jual " + productName + " seharga : " + sellPrice);
            

            // Cek jika ini item valid (bukan 'Memuat...' atau 'Tidak ditemukan')
            if (!productId) return; 

            let currentRow = $(this).closest('tr');
            
            // Isi semua field
            currentRow.find('.product-id').val(productId);
            currentRow.find('.product-search').val(productName); // Isi input utama
            currentRow.find('.category-name').val(categoryName);
            currentRow.find('.sell-price').val(sellPrice);

            // Sembunyikan dropdown
            const dropdownMenu = $(this).closest('.dropdown-menu');
            dropdownMenu.removeClass('show');
            dropdownMenu.siblings('.product-search').attr('aria-expanded', 'false');
        });

        // 4. Hentikan dropdown agar tidak tertutup saat mengklik search bar internal
        $('#stock-table-body').on('click', '.dropdown-search', function(e) {
            e.stopPropagation();
        });

        // 5. Menutup dropdown saat klik di luar area
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
                $('.product-search').attr('aria-expanded', 'false');
            }
        });
    });
</script>
@endpush
    
