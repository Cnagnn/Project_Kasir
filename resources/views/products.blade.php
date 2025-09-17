            @extends('layouts.admin')

            @section('content')
            
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
            
            {{-- SEARCH AND FILTER SECTION --}}
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="searchProduct">Cari Produk</label>
                                <input type="text" class="form-control" id="searchProduct" placeholder="Nama Produk">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kategori</label>
                                
                                <input type="hidden" name="filter_category_id" id="filter_selected_category_id" value="">

                                <div class="btn-group d-block">
                                    {{-- Tombol untuk menampilkan nama kategori terpilih --}}
                                    <button type="button" class="btn btn-primary" id="filter_category_dropdown_button">
                                        Semua Kategori
                                    </button>
                                    {{-- Tombol panah dropdown --}}
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
                                            data-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    
                                    {{-- Daftar pilihan kategori --}}
                                    <ul class="dropdown-menu" id="filter_category_options">
                                        <li>
                                            <a class="dropdown-item active" href="#" data-id="" data-name="Semua Kategori">
                                                Semua Kategori
                                            </a>
                                        </li>
                                        @foreach ($categories as $category)
                                            <li>
                                                <a class="dropdown-item" href="#" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>

            {{-- MAIN PRODUCTS TABLE SECTION --}}
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Products</h4>
                        <div class="btn-wrapper">
                            <button type="button" class="btn btn-outline-primary align-items-center" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="mdi mdi-tag-plus"></i> Kategori Baru
                            </button>
                            <button type="button" class="btn btn-primary text-white me-0" data-toggle="modal" data-target="#addProductModal">
                                <i class="mdi mdi-plus"></i> Buat Produk Baru
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Stok Tersisa</th>
                            <th>Harga Jual</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        {{ $product->category->name ?? 'Tidak ada kategori' }}
                                    </td>
                                    <td>
                                        {{ $product->stockBatches->sum('remaining_stock') }}
                                    </td>
                                    <td>
                                        Rp {{ number_format($product->stockBatches->last()->sell_price ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-icon-only btn-edit btn-sm me-1" title="Edit / Lihat Batch">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="form-delete d-inline">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <button type="submit" class="btn btn-icon-only btn-delete btn-sm" data-name="{{ $product->name }}" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                      </table>
                        
                    </div>
                  </div>
                </div>
              </div>


            {{-- MODAL TAMBAH PRODUK --}}

            <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    
                <div class="modal-dialog modal-dialog-centered" role="document">
                    
                    <div class="modal-content">

                        <form action="{{ route('product.store') }}" method="POST" class="forms-sample material-form">
                            @csrf
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

                                <div class="form-group">
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


            {{-- MODAL TAMBAH KATEGORI --}}

            <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    
                <div class="modal-dialog modal-dialog-centered" role="document">
                    
                    <div class="modal-content">

                        <form action="{{ route('category.store') }}" method="POST" class="forms-sample material-form">
                            @csrf
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


            {{-- MODAL UPDATE PRODUK --}}

            {{-- <div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    
                    <div class="modal-content">
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>SKU</th>
                                        <th>Harga Modal</th>
                                        <th>Harga Jual</th>
                                        <th>Stok</th>
                                        <th>Tanggal Update</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="stockBatchesTable">
                                    <!-- Data akan diisi melalui JavaScript -->
                                    <tr id="noDataRow">
                                        <td colspan="7" class="text-center">Tidak ada data stock batch.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}



            {{-- DOM TOMBOL KATEGORI --}}

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // === ADD PRODUCT MODAL ===
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

                    // === HANDLE UPDATE BUTTON CLICK ===
                    // Handle ketika tombol edit diklik untuk mengisi data ke modal
                    const updateButtons = document.querySelectorAll('[data-target="#updateProductModal"]');
                    
                    updateButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            // Ambil data dari atribut data-*
                            const productId = this.getAttribute('data-id');
                            const productName = this.getAttribute('data-name');
                            const categoryId = this.getAttribute('data-category-id');
                            const categoryName = this.getAttribute('data-category-name');
                            const stock = this.getAttribute('data-stock');
                            const buyPrice = this.getAttribute('data-buy-price');
                            const sellPrice = this.getAttribute('data-sell-price');
                            
                            // Isi form update dengan data produk
                            document.getElementById('update_product_id').value = productId;
                            document.getElementById('update_name').value = productName;
                            document.getElementById('update_stock').value = stock;
                            document.getElementById('update_buy_price').value = buyPrice;
                            document.getElementById('update_sell_price').value = sellPrice;
                            
                            // Set kategori yang terpilih
                            document.getElementById('update_selected_category_id').value = categoryId;
                            document.getElementById('update_category_dropdown_button').textContent = categoryName || '-- Pilih Kategori --';
                            
                            // Update action form dengan ID produk
                            const form = document.getElementById('updateProductForm');
                            const baseAction = "{{ url('/product-update') }}";
                            form.action = baseAction + '/' + productId;
                        });
                    });
                });

                // === HANDLE DELETE BUTTON CLICK ===
                // Pastikan CDN SweetAlert sudah dimuat
                document.addEventListener('DOMContentLoaded', function () {
                    
                    // Cari SEMUA form yang punya class .form-delete
                    const deleteForms = document.querySelectorAll('.form-delete');
                    
                    deleteForms.forEach(form => {
                        // Kita "dengarkan" saat form ini akan di-submit
                        form.addEventListener('submit', function (event) {
                            
                            // 1. HENTIKAN PENGIRIMAN FORM (JANGAN RELOAD DULU)
                            event.preventDefault(); 
                            
                            // Ambil nama dari tombol di dalam form ini
                            const button = form.querySelector('button[type="submit"]');
                            const productName = button.dataset.name;

                            // 2. Tampilkan Pop-up Konfirmasi
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: `Anda akan menghapus "${productName}".`,
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
                        });
                    });

                    // === SEARCH FUNCTIONALITY ===
                    const searchInput = document.getElementById('searchProduct');
                    
                    // Search functionality placeholder
                    if (searchInput) {
                        searchInput.addEventListener('input', function() {
                            const searchTerm = this.value.toLowerCase();
                            // TODO: Implement search logic
                            console.log('Searching for:', searchTerm);
                        });
                    }

                    // === FILTER CATEGORY DROPDOWN (SAME AS BATCH.BLADE.PHP) ===
                    $('#filter_category_options .dropdown-item').on('click', function(event) {
                        event.preventDefault(); // Mencegah link # beraksi
                        var selectedId = $(this).data('id');
                        var selectedName = $(this).data('name');
                        $('#filter_selected_category_id').val(selectedId);
                        $('#filter_category_dropdown_button').text(selectedName);
                        $('#filter_category_options .dropdown-item').removeClass('active');
                        $(this).addClass('active');
                        
                        // TODO: Implement filter logic
                        console.log('Filter by category:', selectedId, selectedName);
                    });
                });
            </script>

            <style>
                /* Button icon-only styles */
                .btn-icon-only {
                    background: transparent;
                    border: none;
                    padding: 0.375rem;
                    border-radius: 0.375rem;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s ease-in-out;
                    min-width: 32px;
                    height: 32px;
                }

                .btn-icon-only i {
                    color: #333;
                    font-size: 16px;
                    transition: color 0.2s ease-in-out;
                }

                /* Edit button hover - yellow */
                .btn-edit:hover {
                    background-color: rgba(255, 193, 7, 0.1);
                }

                .btn-edit:hover i {
                    color: #ffc107;
                }

                /* Delete button hover - red */
                .btn-delete:hover {
                    background-color: rgba(220, 53, 69, 0.1);
                }

                .btn-delete:hover i {
                    color: #dc3545;
                }

                /* Focus states */
                .btn-icon-only:focus {
                    outline: none;
                    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
                }

                /* Custom button styling */
                .btn-outline-primary {
                    color: #333 !important;
                    border-color: #007bff;
                }

                .btn-outline-primary:hover {
                    color: #fff !important;
                    background-color: #007bff;
                    border-color: #007bff;
                }

                /* Custom table-bordered - horizontal borders only + left/right edges */
                .table-bordered {
                    border-left: 1px solid #dee2e6 !important;
                    border-right: 1px solid #dee2e6 !important;
                    border-top: 1px solid #dee2e6 !important;
                    border-bottom: 1px solid #dee2e6 !important;
                }

                .table-bordered th,
                .table-bordered td {
                    border-left: none !important;
                    border-right: none !important;
                    border-top: none !important;
                    border-bottom: 1px solid #dee2e6 !important;
                }

                /* First column - add left border */
                .table-bordered th:first-child,
                .table-bordered td:first-child {
                    border-left: none !important;
                }

                /* Last column - add right border */
                .table-bordered th:last-child,
                .table-bordered td:last-child {
                    border-right: none !important;
                }

                /* Header row - add top border */
                .table-bordered thead th {
                    border-top: none !important;
                }

                /* Remove bottom border from last row */
                .table-bordered tbody tr:last-child td {
                    border-bottom: none !important;
                }

                /* Search and Filter Section Styling */
                .card-body.border-bottom {
                    border-bottom: 1px solid #edf2f7 !important;
                    background: #f8f9fa;
                    padding: 1.5rem;
                }

                .input-group-text {
                    border: 1px solid #ced4da;
                    background-color: #007bff;
                    color: white;
                    font-size: 14px;
                    min-width: 45px;
                    justify-content: center;
                }

                .input-group .form-control {
                    border-left: none;
                    font-size: 14px;
                    height: 44px;
                }

                .input-group .form-control:focus {
                    border-color: #007bff;
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                }

                .form-control {
                    height: 44px;
                    font-size: 14px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                }

                .form-control:focus {
                    border-color: #007bff;
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                }

                /* Form group spacing */
                .form-group {
                    margin-bottom: 0;
                }

                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .card-body.border-bottom {
                        padding: 1rem;
                    }
                }
            </style>

            @endsection

            