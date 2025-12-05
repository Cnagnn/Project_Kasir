@extends('layouts.admin')

@section('page-title', 'Stok Barang')
@section('page-description', 'Monitor dan kelola stok barang di gudang')

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

{{-- SWEATALERT --}}

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

            @if(session()->has('product_add_success'))
                <script>
                    Swal.fire({
                        title: "BERHASIL",
                        text: "{{ session('product_add_success') }}",
                        icon: "success"
                    });
                </script>    
            @endif
            @if(session()->has('category_add_success'))
                <script>
                    Swal.fire({
                        title: "BERHASIL",
                        text: "{{ session('category_add_success') }}",
                        icon: "success"
                    });
                </script>    
            @endif
            @if(session()->has('product_update_success'))
                <script>
                    Swal.fire({
                        title: "BERHASIL",
                        text: "{{ session('product_update_success') }}",
                        icon: "success"
                    });
                </script>    
            @endif
            @if(session()->has('product_delete_success'))
                <script>
                    Swal.fire({
                        title: "BERHASIL",
                        text: "{{ session('product_update_success') }}",
                        icon: "success"
                    });
                </script>    
            @endif

{{-- END SWEATALERT --}}

<div class="row">
    <div class="col-sm-12" style="padding-left: 0; padding-right: 0;">

        {{-- MAIN CONTENT CONTAINER --}}
        <div id="mainContentContainer">
            
            {{-- SEARCH AND FILTER SECTION --}}

            <div class="col-lg-12 grid-margin stretch-card" id="searchSection">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="searchProduct">Cari Produk</label>
                                    <input type="text" class="form-control" id="searchProduct" placeholder="Nama Produk">
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>

                {{-- END SEARCH AND FILTER SECTION --}}

                {{-- SEARCH PRODUCT BOX --}}

                <div class="col-lg-12" id="searchResultsContainer">
                    {{-- Search results will be displayed here by JavaScript --}}
                </div>

                {{-- END SEARCH PRODUCT BOX --}}

        
        {{-- MAIN TABLE / PRODUCT LIST --}}
        
        <div class="col-lg-12 grid-margin stretch-card" id="mainProductTable">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Daftar Stok Produk</h4>
                        {{-- <div class="btn-wrapper">
                            @if (Auth::user()->role->name != "Cashier")
                                <button type="button" class="btn btn-outline-primary me-0" data-toggle="modal" data-target="#addCategoryModal">
                                    <i class="mdi mdi-plus"></i> Tambah Kategori
                                </button>    
                                <button type="button" class="btn btn-outline-primary me-0" data-toggle="modal" data-target="#addProductModal">
                                    <i class="mdi mdi-plus"></i> Tambah Product
                                </button>
                            @endif
                        </div> --}}
                    </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-centered">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                @php
                                    $totalStock = $product->stock->sum('remaining_stock');
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? 'Tidak ada kategori' }}</td>
                                    <td>{{ $totalStock }}</td>
                                    <td>
                                        <div class="action-btn-group" role="group" aria-label="Aksi stok">
                                            <button class="btn btn-primary btn-sm btn-view-detail" data-product-id="{{ $product->id }}">
                                                <i class="mdi mdi-information-outline"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>

            {{-- MAIN TABLE / PRODUCT LIST --}}

        </div>
        {{-- END MAIN CONTENT CONTAINER --}}

        {{-- DETAIL CONTENT CONTAINER (Hidden by default) --}}
        <div id="detailContentContainer" style="display: none;">
            {{-- Detail content will be loaded here via AJAX --}}
        </div>
        {{-- END DETAIL CONTENT CONTAINER --}}
            {{-- MODAL ADD PRODUCT --}}
            @if (Auth::user()->role->name != "Cashier")
                <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        
                        <div class="modal-content">

                            <form action="{{ route('product.store') }}" method="POST" class="forms-sample material-form">
                                @csrf
                                <input type="hidden" value="product_page" name="page">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addProductModalLabel">Tambah Produk Baru</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <p class="card-description">Isi detail produk di bawah ini.</p>

                                    <div class="form-group">
                                        <input type="text" class="form-control" id="name" name="name" required="required" />
                                        <label for="name" class="control-label">Nama Produk</label><i class="bar"></i>
                                    </div>

                                    <div class="form-group">
                                        <label>Kategori</label>
                                        
                                        <input type="hidden" name="category_id" id="selected_category_id" required>

                                        <div class="btn-group d-block">
                                            <button type="button" class="btn btn-outline-primary" id="category_dropdown_button">
                                                -- Pilih Kategori --
                                            </button>
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            
                                            <ul class="dropdown-menu" id="category_options">
                                                {{-- Loop untuk menampilkan semua kategori yang tersedia --}}
                                                @foreach ($categories as $category)
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                            {{ $category->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <small class="form-text text-muted">Klik panah untuk memilih kategori.</small>
                                    </div>

                                    {{-- <div class="form-group">
                                        <input type="number" class="form-control" id="stock" name="stock" required="required" />
                                        <label for="stock" class="control-label">Stok</label><i class="bar"></i>
                                    </div>

                                    <div class="form-group">
                                        <input type="number" class="form-control" id="buy_price" name="buy_price" required="required" />
                                        <label for="price" class="control-label">Harga Beli</label><i class="bar"></i>
                                    </div>

                                    <div class="form-group">
                                        <input type="number" class="form-control" id="sell_price" name="sell_price" required="required" />
                                        <label for="price" class="control-label">Harga Jual</label><i class="bar"></i>
                                    </div> --}}
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="button btn btn-primary"><span>Simpan</span></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- END MODAL ADD PRODUCT --}}


                {{-- MODAL ADD CATEGORY --}}

                <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        
                        <div class="modal-content">

                            <form action="{{ route('category.store') }}" method="POST" class="forms-sample material-form">
                                @csrf
                                <input type="hidden" value="product_page" name="page">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addProductModalLabel">Tambah Kategori Baru</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="name" name="name" required="required" />
                                        <label for="name" class="control-label">Nama Kategori</label><i class="bar"></i>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="button btn btn-primary"><span>Simpan</span></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                {{-- END MODAL ADD CATEGORY --}}
                
                {{-- MODAL EDIT PRODUCT --}}

                <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
        
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        
                        <div class="modal-content">

                            <form action="{{ route('product.update') }}" method="POST" class="forms-sample material-form" id="editProductForm">
                                @csrf
                                <input type="hidden" id="product_id" name="product_id">
                                {{-- <input type="hidden" id="category_id" name="categoryId"> --}}
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="product_name" name="product_name" required="required" />
                                        <label for="name" class="control-label">Nama Product</label><i class="bar"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                        <label>Kategori</label>
                                        
                                        <input type="hidden" name="category_id" id="selected_category_id" required>

                                        <div class="btn-group d-block">
                                            <button type="button" class="btn btn-outline-primary" id="category_dropdown_button">
                                                -- Pilih Kategori --
                                            </button>
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            
                                            <ul class="dropdown-menu" id="category_options">
                                                {{-- Loop untuk menampilkan semua kategori yang tersedia --}}
                                                @foreach ($categories as $category)
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                            {{ $category->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <small class="form-text text-muted">Klik panah untuk memilih kategori.</small>
                                    </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="button btn btn-primary"><span>Simpan</span></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                {{-- END MODAL EDIT PRODUCT --}}
            @endif

            

    </div>
</div>

@endsection

            
            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    // {{-- DOM TOMBOL KATEGORI --}}
                    // Cari semua item kategori di dalam dropdown add product
                    const categoryItems = document.querySelectorAll('#category_options .dropdown-item');
                    
                    // Cari tombol utama dan input tersembunyi add product
                    const dropdownButton = document.getElementById('category_dropdown_button');
                    const hiddenInput = document.getElementById('selected_category_id');

                    // Tambahkan event listener untuk setiap item kategori add product
                    categoryItems.forEach(item => {
                        item.addEventListener('click', function (event) {
                            // Mencegah link berpindah halaman
                            event.preventDefault(); 
                            
                            // Ambil ID dan Nama dari atribut data-*
                            const selectedId = this.getAttribute('data-id');
                            const selectedName = this.getAttribute('data-name');
                            
                            // Perbarui teks pada tombol utama
                            dropdownButton.textContent = selectedName;
                            
                            // Simpan ID kategori ke input tersembunyi (ini yang akan dikirim ke server)
                            hiddenInput.value = selectedId;
                        });
                    });

                    // {{-- END DOM TOMBOL KATEGORI --}}

                    // === UPDATE PRODUCT MODAL ===
                    // Cari semua item kategori di dalam dropdown update product
                    const updateCategoryItems = document.querySelectorAll('#update_category_options .dropdown-item');
                    
                    // Cari tombol utama dan input tersembunyi update product
                    const updateDropdownButton = document.getElementById('update_category_dropdown_button');
                    const updateHiddenInput = document.getElementById('update_selected_category_id');

                    // Tambahkan event listener untuk setiap item kategori update product
                    updateCategoryItems.forEach(item => {
                        item.addEventListener('click', function (event) {
                            // Mencegah link berpindah halaman
                            event.preventDefault(); 
                            
                            // Ambil ID dan Nama dari atribut data-*
                            const selectedId = this.getAttribute('data-id');
                            const selectedName = this.getAttribute('data-name');
                            
                            // Perbarui teks pada tombol utama
                            updateDropdownButton.textContent = selectedName;
                            
                            // Simpan ID kategori ke input tersembunyi (ini yang akan dikirim ke server)
                            updateHiddenInput.value = selectedId;
                        });
                    });
                });

                // === HANDLE DELETE BUTTON CLICK ===
                // Pastikan CDN SweetAlert sudah dimuat
                // document.addEventListener('DOMContentLoaded', function () {
                    
                //     // Cari SEMUA form yang punya class .form-delete
                //     const deleteForms = document.querySelectorAll('.form-delete');
                    
                //     deleteForms.forEach(form => {
                //         // Kita "dengarkan" saat form ini akan di-submit
                //         form.addEventListener('submit', function (event) {
                            
                //             // 1. HENTIKAN PENGIRIMAN FORM (JANGAN RELOAD DULU)
                //             event.preventDefault(); 
                            
                //             // Ambil nama dari tombol di dalam form ini
                //             const button = form.querySelector('button[type="submit"]');
                //             const productName = button.dataset.name;

                //             // 2. Tampilkan Pop-up Konfirmasi
                //             Swal.fire({
                //                 title: 'Apakah Anda yakin?',
                //                 text: `Anda akan menghapus "${productName}".`,
                //                 icon: 'warning',
                //                 showCancelButton: true,
                //                 confirmButtonColor: '#d33',
                //                 confirmButtonText: 'Ya, hapus!',
                //                 cancelButtonText: 'Batal'
                //             }).then((result) => {
                //                 // 3. JIKA PENGGUNA KLIK "YA"
                //                 if (result.isConfirmed) {
                //                     // Lanjutkan proses submit form (SEKARANG HALAMAN AKAN RELOAD)
                //                     form.submit(); 
                //                 }
                //             });
                //         });
                //     });
                // });

                document.addEventListener('DOMContentLoaded', function() {
    
                    // === SEARCH FUNCTIONALITY ===
                    const searchInput = document.getElementById('searchProduct');
                    const resultsContainer = document.getElementById('searchResultsContainer');
                    const mainProductTable = document.getElementById('mainProductTable');

                    // Pastikan semua elemen ada sebelum menambahkan event listener
                    if (searchInput && resultsContainer && mainProductTable) {
                        
                        searchInput.addEventListener('input', function() {
                            const searchTerm = this.value.trim();
                            console.log(searchTerm);

                            if (searchTerm === '') {
                                resultsContainer.innerHTML = '';
                                resultsContainer.style.display = 'none';
                                mainProductTable.style.display = 'block';
                                return;
                            }

                            resultsContainer.style.display = 'block';
                            mainProductTable.style.display = 'none';

                            fetch(`/product/search?query=${encodeURIComponent(searchTerm)}`)
                                // 1. Kembali menggunakan .json() karena menerima data
                                .then(response => response.json())
                                .then(data => {
                                    resultsContainer.innerHTML = ''; // Kosongkan hasil lama

                                    // 2. Buat struktur card dan tabel secara dinamis
                                    const resultsCard = document.createElement('div');
                                    resultsCard.className = 'card card-rounded';
                                    const stockDetailBase = @json(url('item-stock/detail'));

                                    let cardContent = `
                                        <div class="card-body">
                                            <h4 class="card-title mb-4">Hasil Pencarian untuk "${searchTerm}"</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover table-centered">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama</th>
                                                            <th>Kategori</th>
                                                            <th>Stok</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                    `;

                                    // 3. Cek jika ada hasil
                                    if (data.length > 0) {
                                        // 4. Loop setiap produk dan buat baris tabel (<tr>)
                                        data.forEach((product, index) => {
                                            const categoryName = product.category ? product.category.name : 'Tidak ada kategori';
                                            const totalStock = product.stock.reduce((sum, batch) => sum + batch.remaining_stock, 0);

                                            cardContent += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${product.name}</td>
                                                    <td>${categoryName}</td>
                                                    <td>${totalStock}</td>
                                                    <td>
                                                        <div class="action-btn-group" role="group" aria-label="Aksi stok">
                                                            <button class="btn btn-primary btn-sm btn-view-detail" data-product-id="${product.id}">
                                                                <i class="mdi mdi-information-outline"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `;
                                        });
                                    } else {
                                        // Jika tidak ada hasil
                                        cardContent += `
                                            <tr>
                                                <td colspan="5" class="text-center">Produk tidak ditemukan.</td>
                                            </tr>
                                        `;
                                    }

                                    // 5. Tutup tag html
                                    cardContent += `
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    `;

                                    // 6. Masukkan semua HTML yang sudah jadi ke dalam card dan tampilkan
                                    resultsCard.innerHTML = cardContent;
                                    resultsContainer.appendChild(resultsCard);
                                })
                                .catch(error => {
                                    console.error('Error fetching search results:', error);
                                });
                        });

                    } else {
                        // Jika salah satu elemen tidak ditemukan, log error ke console
                        console.error('Satu atau lebih elemen untuk fungsionalitas pencarian tidak ditemukan!');
                    }
                });


                $(function(){
                     $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }); 
                    // Gunakan event delegation agar tombol yang baru dimuat (misal via live search) tetap berfungsi
                    $(document).on('click', '.btn-add-to-cart', function(e) {
                        console.log('Tombol .btn-add-to-cart berhasil diklik!');    
                        e.preventDefault(); // Mencegah aksi default dari tombol

                        var productId = $(this).data('id'); // Ambil product_id dari atribut data-id
                        var button = $(this); // Simpan referensi tombol
                        console.log(productId);
                        // Kirim request AJAX ke server
                        $.ajax({
                            url: "{{ route('cart.addToCart') }}", // URL ke controller
                            method: "POST",
                            data: {
                                product_id: productId
                            },
                            // Aksi jika request berhasil
                            success: function(response) {
                                console.log(response); // Untuk debug

                                // Beri feedback ke user (contoh menggunakan SweetAlert atau Toastr)
                                // alert(response.message);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });

                                // Opsional: Update angka di ikon keranjang
                                // Misal Anda punya <span id="cart-count">0</span> di navbar
                                $('#cart-count').text(response.cart_count);
                            },
                            // Aksi jika request gagal
                            error: function(xhr, status, error) {
                                console.error("Terjadi kesalahan: " + error);
                                // Tampilkan pesan error ke user
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal menambahkan produk!',
                                });
                            }
                        });
                    });
                });

                $(document).ready(function () {

                    // 1. Menangani event klik pada tombol edit
                    $(document).on('click', '.edit-product-btn', function () {
                        // Ambil data dari tombol yang di-klik
                        let productId = $(this).data('productid');
                        let productName = $(this).data('productname');
                        let categoryId = $(this).data('categoryid');
                        let categoryName = $(this).data('categoryname');

                        // Isi value input nama kategori dengan nama yang sekarang
                        $('#editProductForm #product_id').val(productId);
                        $('#editProductForm #product_name').val(productName);
                        $('#editProductForm #selected_category_id').val(categoryId);
                        $('#editProductForm #category_dropdown_button').text(categoryName);
                        console.log(productId);
                        console.log(productName);
                        console.log(categoryId);
                        console.log(categoryName);
                        // console.log(url);
                        // Hapus pesan error sebelumnya (jika ada)
                        $('#editCategoryForm #category_name').removeClass('is-invalid');
                        $('#editCategoryForm #name_error').text('');

                        // [INI KUNCINYA] Tampilkan modal via JavaScript
                        $('#editProductModal').modal('show');

                    });

                });

                // === HANDLE VIEW DETAIL BUTTON ===
                $(document).on('click', '.btn-view-detail', function(e) {
                    e.preventDefault();
                    const productId = $(this).data('product-id');
                    
                    // Show loading state
                    $('#detailContentContainer').html('<div class="text-center p-5"><i class="mdi mdi-loading mdi-spin" style="font-size: 48px;"></i><p>Memuat detail...</p></div>');
                    $('#detailContentContainer').show();
                    $('#mainContentContainer').hide();
                    
                    // Fetch detail content via AJAX
                    $.ajax({
                        url: `/item-stock/detail/${productId}`,
                        method: 'GET',
                        success: function(response) {
                            $('#detailContentContainer').html(response);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading detail:', error);
                            $('#detailContentContainer').html(`
                                <div class="alert alert-danger m-3">
                                    <h4>Error</h4>
                                    <p>Gagal memuat detail produk. Silakan coba lagi.</p>
                                    <button class="btn btn-primary btn-back-to-list">
                                        <i class="mdi mdi-arrow-left"></i> Kembali
                                    </button>
                                </div>
                            `);
                        }
                    });
                });

                // === HANDLE BACK TO LIST BUTTON ===
                $(document).on('click', '.btn-back-to-stock-list', function(e) {
                    e.preventDefault();
                    $('#detailContentContainer').hide().html('');
                    $('#mainContentContainer').show();
                });
                

            </script>
            @endpush

            