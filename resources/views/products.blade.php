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
            
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Products</h4>
                        <div class="btn-wrapper">
                            <button type="button" class="btn btn-otline-dark align-items-center" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="mdi mdi-tag-plus"></i> Add Category
                            </button>
                            <button type="button" class="btn btn-primary text-white me-0" data-toggle="modal" data-target="#addProductModal">
                                <i class="mdi mdi-plus"></i> Add Product
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Action</th>
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
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="mdi mdi-pencil"></i> Edit / Lihat Batch
                                        </a>
                                        <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="form-delete d-inline">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <button type="submit" class="btn btn-danger btn-sm" data-name="{{ $product->name }}">
                                                <i class="mdi mdi-delete"></i> Delete
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
                });
            </script>

            @endsection

            