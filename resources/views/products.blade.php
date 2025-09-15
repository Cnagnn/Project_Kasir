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
                                <i class="icon-tag"></i> Add Category
                            </button>
                            <button type="button" class="btn btn-primary text-white me-0" data-toggle="modal" data-target="#addProductModal">
                                <i class="icon-plus"></i> Add Product
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
                                        {{ $product->ProductStockBatches->sum('remaining_stock') }}
                                    </td>
                                    <td>
                                        Rp {{ number_format($product->ProductStockBatches->last()->sell_price ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-otline-dark btn-sm me-1" data-toggle="modal" data-target="#updateProductModal" 
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-category-id="{{ $product->category_id }}"
                                                data-category-name="{{ $product->category->name ?? '' }}"
                                                data-stock="{{ $product->ProductStockBatches->sum('remaining_stock') }}"
                                                data-buy-price="{{ $product->ProductStockBatches->last()->buy_price ?? 0 }}"
                                                data-sell-price="{{ $product->ProductStockBatches->last()->sell_price ?? 0 }}"
                                                title="Edit">
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="form-delete d-inline">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <button type="submit" class="btn btn-otline-dark btn-sm" data-name="{{ $product->name }}" title="Delete">
                                                <i class="icon-trash"></i>
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
                                <button type="button" class="btn btn-otline-dark" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary text-white me-0"><span>Simpan</span></button>
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
                                <button type="button" class="btn btn-otline-dark" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary text-white me-0"><span>Simpan</span></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            {{-- MODAL UPDATE PRODUK --}}

            <div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel" aria-hidden="true" data-bs-keyboard="false">
    
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="updateProductModalLabel">Update Produk</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <!-- Form Update Nama Produk dan Kategori -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0" style="font-size: 0.875rem;"><i class="mdi mdi-information-outline"></i> Detail</h6>
                                    </div>
                                    <form id="updateProductInfoForm" class="forms-sample material-form">
                                        <input type="hidden" id="update_product_id" name="product_id">
                                        
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="update_name" name="name" required />
                                            <label for="update_name" class="control-label">Nama Produk</label><i class="bar"></i>
                                        </div>

                                        <div class="form-group">
                                            <label>Kategori</label>
                                            
                                            <input type="hidden" name="category_id" id="update_selected_category_id" required>

                                            <div class="btn-group d-block">
                                                <button type="button" class="btn btn-outline-primary" id="update_category_dropdown_button">
                                                    -- Pilih Kategori --
                                                </button>
                                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                
                                                <ul class="dropdown-menu" id="update_category_options">
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
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Stock Batches Section -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0" style="font-size: 0.875rem;"><i class="mdi mdi-package-variant"></i> Stock Batches</h6>
                                        <button type="button" class="btn btn-primary text-white me-0 btn-sm" id="addNewBatchBtn">
                                            <i class="icon-plus"></i> Tambah Batch Baru
                                        </button>
                                    </div>

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
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary text-white me-0" id="saveProductInfoBtn">
                                <i class="icon-check"></i> Simpan
                            </button>
                            <button type="button" class="btn btn-otline-dark" data-dismiss="modal">Tutup</button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- MODAL EDIT BATCH --}}
            <div class="modal fade" id="editBatchModal" tabindex="-1" role="dialog" aria-labelledby="editBatchModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-right" role="document">
                    <div class="modal-content">
                        <form id="editBatchForm" class="forms-sample material-form">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editBatchModalLabel">Edit Batch</h5>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="editBatchId">
                                
                                <div class="form-group">
                                    <input type="text" class="form-control" id="editSku" required />
                                    <label for="editSku" class="control-label">SKU</label><i class="bar"></i>
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" id="editBuyPrice" required />
                                    <label for="editBuyPrice" class="control-label">Harga Modal</label><i class="bar"></i>
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" id="editSellPrice" required />
                                    <label for="editSellPrice" class="control-label">Harga Jual</label><i class="bar"></i>
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" id="editStock" required />
                                    <label for="editStock" class="control-label">Stok</label><i class="bar"></i>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary text-white me-0">
                                    <i class="icon-check"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- MODAL TAMBAH BATCH BARU --}}
            <div class="modal fade" id="addBatchModal" tabindex="-1" role="dialog" aria-labelledby="addBatchModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="addBatchForm" class="forms-sample material-form">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addBatchModalLabel">Tambah Stock Batch Baru</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="addBatchProductId">
                                
                                <div class="form-group">
                                    <input type="text" class="form-control" id="newSku" required />
                                    <label for="newSku" class="control-label">SKU</label><i class="bar"></i>
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" id="newBuyPrice" required />
                                    <label for="newBuyPrice" class="control-label">Harga Modal</label><i class="bar"></i>
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" id="newSellPrice" required />
                                    <label for="newSellPrice" class="control-label">Harga Jual</label><i class="bar"></i>
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" id="newStock" required />
                                    <label for="newStock" class="control-label">Stok</label><i class="bar"></i>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-otline-dark" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary text-white me-0">
                                    <i class="icon-plus"></i> Tambah Batch
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

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
                    // Handle ketika tombol edit diklik untuk menampilkan tabel stock batches
                    const updateButtons = document.querySelectorAll('[data-target="#updateProductModal"]');
                    
                    updateButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            // Ambil data dari atribut data-*
                            const productId = this.getAttribute('data-id');
                            const productName = this.getAttribute('data-name');
                            const categoryId = this.getAttribute('data-category-id');
                            const categoryName = this.getAttribute('data-category-name');
                            
                            // Fill form informasi produk
                            document.getElementById('update_product_id').value = productId;
                            document.getElementById('update_name').value = productName;
                            document.getElementById('update_selected_category_id').value = categoryId;
                            document.getElementById('update_category_dropdown_button').textContent = categoryName || '-- Pilih Kategori --';
                            
                            // Load stock batches data (simulasi data - biasanya dari server)
                            loadStockBatches(productId, productName);
                        });
                    });

                    // === FUNCTION TO LOAD STOCK BATCHES ===
                    function loadStockBatches(productId, productName) {
                        const tbody = document.getElementById('stockBatchesTable');
                        const noDataRow = document.getElementById('noDataRow');
                        
                        // Simulasi data stock batches (dalam implementasi nyata, data ini dari server/API)
                        const sampleBatches = [
                            {
                                id: 1,
                                sku: 'SKU001',
                                buy_price: 50000,
                                sell_price: 75000,
                                remaining_stock: 25,
                                updated_at: '2025-09-15 10:30:00'
                            },
                            {
                                id: 2,
                                sku: 'SKU002',
                                buy_price: 48000,
                                sell_price: 72000,
                                remaining_stock: 15,
                                updated_at: '2025-09-14 14:15:00'
                            },
                            {
                                id: 3,
                                sku: 'SKU003',
                                buy_price: 52000,
                                sell_price: 78000,
                                remaining_stock: 30,
                                updated_at: '2025-09-13 09:45:00'
                            }
                        ];
                        
                        // Clear existing rows
                        tbody.innerHTML = '';
                        
                        if (sampleBatches.length > 0) {
                            sampleBatches.forEach((batch, index) => {
                                const row = createBatchRow(batch, index);
                                tbody.appendChild(row);
                            });
                        } else {
                            tbody.appendChild(noDataRow);
                        }
                        
                        // Store current product ID for add new batch
                        document.getElementById('addBatchProductId').value = productId;
                    }

                    // === FUNCTION TO CREATE BATCH ROW ===
                    function createBatchRow(batch, index) {
                        const row = document.createElement('tr');
                        
                        const formatDate = new Date(batch.updated_at).toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                        
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${batch.sku}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(batch.buy_price)}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(batch.sell_price)}</td>
                            <td>${batch.remaining_stock}</td>
                            <td>${formatDate}</td>
                            <td>
                                <button type="button" class="btn btn-otline-dark btn-sm me-1" onclick="editBatch(${batch.id}, '${batch.sku}', ${batch.buy_price}, ${batch.sell_price}, ${batch.remaining_stock})" title="Edit">
                                    <i class="icon-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-otline-dark btn-sm" onclick="deleteBatch(${batch.id}, '${batch.sku}')" title="Delete">
                                    <i class="icon-trash"></i>
                                </button>
                            </td>
                        `;
                        
                        return row;
                    }

                    // === ADD NEW BATCH BUTTON ===
                    document.getElementById('addNewBatchBtn').addEventListener('click', function () {
                        $('#addBatchModal').modal('show');
                    });

                    // === HANDLE CATEGORY DROPDOWN IN UPDATE MODAL ===
                    // Event listener untuk dropdown kategori di modal update sudah ada di atas
                    // Tapi kita perlu memastikan ia bekerja untuk update modal juga
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

                // === GLOBAL FUNCTIONS FOR BATCH MANAGEMENT ===
                
                // Function to edit batch
                window.editBatch = function(id, sku, buyPrice, sellPrice, stock) {
                    document.getElementById('editBatchId').value = id;
                    document.getElementById('editSku').value = sku;
                    document.getElementById('editBuyPrice').value = buyPrice;
                    document.getElementById('editSellPrice').value = sellPrice;
                    document.getElementById('editStock').value = stock;
                    
                    // Add class to shift main modal to left
                    document.getElementById('updateProductModal').classList.add('modal-shifted');
                    
                    // Show modal using Bootstrap 5
                    const editBatchModal = new bootstrap.Modal(document.getElementById('editBatchModal'));
                    editBatchModal.show();
                };

                // Function to delete batch
                window.deleteBatch = function(id, sku) {
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: `Anda akan menghapus batch "${sku}".`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Simulasi penghapusan (dalam implementasi nyata, kirim request ke server)
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Batch "${sku}" telah dihapus.`,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Remove row dari tabel
                            const row = event.target.closest('tr');
                            if (row) {
                                row.remove();
                            }
                            
                            // Check if table is empty
                            const tbody = document.getElementById('stockBatchesTable');
                            if (tbody.children.length === 0) {
                                const noDataRow = document.createElement('tr');
                                noDataRow.id = 'noDataRow';
                                noDataRow.innerHTML = `
                                    <td colspan="7" class="text-center">Tidak ada data stock batch.</td>
                                `;
                                tbody.appendChild(noDataRow);
                            }
                        }
                    });
                };

                // === HANDLE MODAL BATCH EVENTS ===
                const editBatchModal = document.getElementById('editBatchModal');
                
                // Handle modal hidden event (Bootstrap 5 way)
                editBatchModal.addEventListener('hidden.bs.modal', function() {
                    // Remove class to restore main modal position
                    document.getElementById('updateProductModal').classList.remove('modal-shifted');
                    
                    // Reset form
                    document.getElementById('editBatchForm').reset();
                    
                    // Clear form values
                    document.getElementById('editBatchId').value = '';
                    document.getElementById('editSku').value = '';
                    document.getElementById('editBuyPrice').value = '';
                    document.getElementById('editSellPrice').value = '';
                    document.getElementById('editStock').value = '';
                });



                // Handle ESC key behavior for both modals
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        // Allow ESC to close batch modal if it's open
                        if (editBatchModal.classList.contains('show')) {
                            const modal = bootstrap.Modal.getInstance(editBatchModal);
                            if (modal) {
                                modal.hide();
                            }
                        }
                        // Prevent ESC from closing update product modal
                        else if (document.getElementById('updateProductModal').classList.contains('show')) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                    }
                });

                // Additional fallback to ensure main modal is always centered when batch modal is closed
                // This runs on any state change of the edit batch modal
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            const editBatchModal = mutation.target;
                            if (!editBatchModal.classList.contains('show')) {
                                // If batch modal is not showing, ensure main modal is centered
                                const updateModal = document.getElementById('updateProductModal');
                                if (updateModal.classList.contains('modal-shifted')) {
                                    updateModal.classList.remove('modal-shifted');
                                }
                            }
                        }
                    });
                });

                // Start observing the editBatchModal for class changes
                observer.observe(editBatchModal, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                // === HANDLE EDIT BATCH FORM SUBMISSION ===
                document.getElementById('editBatchForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const id = document.getElementById('editBatchId').value;
                    const sku = document.getElementById('editSku').value;
                    const buyPrice = document.getElementById('editBuyPrice').value;
                    const sellPrice = document.getElementById('editSellPrice').value;
                    const stock = document.getElementById('editStock').value;
                    
                    // Simulasi update (dalam implementasi nyata, kirim request ke server)
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Batch "${sku}" telah diperbarui.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Update tabel (dalam implementasi nyata, reload data dari server)
                    const currentProductId = document.getElementById('addBatchProductId').value;
                    const currentProductName = document.getElementById('update_name').value;
                    loadStockBatches(currentProductId, currentProductName);
                    
                    // Close modal using Bootstrap 5 method
                    const modal = bootstrap.Modal.getInstance(editBatchModal) || new bootstrap.Modal(editBatchModal);
                    modal.hide();
                });

                // === HANDLE ADD BATCH FORM SUBMISSION ===
                document.getElementById('addBatchForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const productId = document.getElementById('addBatchProductId').value;
                    const sku = document.getElementById('newSku').value;
                    const buyPrice = document.getElementById('newBuyPrice').value;
                    const sellPrice = document.getElementById('newSellPrice').value;
                    const stock = document.getElementById('newStock').value;
                    
                    // Simulasi penambahan (dalam implementasi nyata, kirim request ke server)
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Batch baru "${sku}" telah ditambahkan.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Reset form
                    this.reset();
                    
                    // Update tabel (dalam implementasi nyata, reload data dari server)
                    const currentProductName = document.getElementById('update_name').value;
                    loadStockBatches(productId, currentProductName);
                    
                    $('#addBatchModal').modal('hide');
                });

                // === HANDLE SAVE PRODUCT INFO BUTTON CLICK ===
                document.getElementById('saveProductInfoBtn').addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const productId = document.getElementById('update_product_id').value;
                    const productName = document.getElementById('update_name').value;
                    const categoryId = document.getElementById('update_selected_category_id').value;
                    const categoryName = document.getElementById('update_category_dropdown_button').textContent;
                    
                    // Validasi form
                    if (!productName.trim()) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Nama produk tidak boleh kosong.',
                            icon: 'error'
                        });
                        return;
                    }
                    
                    if (!categoryId) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Silakan pilih kategori.',
                            icon: 'error'
                        });
                        return;
                    }
                    
                    // Simulasi update (dalam implementasi nyata, kirim request ke server)
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Informasi produk "${productName}" telah diperbarui.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Dalam implementasi nyata, Anda perlu:
                    // 1. Kirim data ke server
                    // 2. Reload halaman atau update tabel utama
                    // 3. Handle error jika ada
                });
            </script>

            <style>
                /* Custom styles for modal */
                #updateProductModal .modal-dialog {
                    max-width: 900px;
                    transition: transform 0.3s ease-out;
                }
                
                /* Shift main modal to left when batch modal is open - calculated for perfect centering */
                #updateProductModal.modal-shifted .modal-dialog {
                    transform: translateX(-210px); /* Centers the combination: (900px + 20px gap + 400px) / 2 = 660px, so shift left by 210px from center */
                }
                
                /* For very large screens - maintain centering */
                @media (min-width: 1600px) {
                    #updateProductModal.modal-shifted .modal-dialog {
                        transform: translateX(-210px);
                    }
                    
                    .modal-dialog-right {
                        left: calc(50% + 260px);
                        width: 450px;
                    }
                }
                
                #updateProductModal .modal-body {
                    padding: 0.75rem;
                }
                
                #updateProductModal .card-body {
                    padding: 0.75rem;
                }
                
                /* Extra compact padding for updateProductInfoForm card */
                #updateProductInfoForm .card-body,
                #updateProductInfoForm.forms-sample {
                    padding: 0.5rem;
                }
                
                #updateProductModal .card-description {
                    margin-bottom: 0.75rem;
                    color: #6c757d;
                    font-size: 0.875rem;
                }
                
                #updateProductModal .card {
                    margin-bottom: 0.75rem;
                }
                
                .modal-header {
                    background-color: #f8f9fa;
                    border-bottom: 1px solid #dee2e6;
                    padding: 0.75rem 1rem;
                }
                
                .modal-footer {
                    padding: 0.75rem 1rem;
                    border-top: 1px solid #dee2e6;
                }
                
                .modal-title {
                    color: #495057;
                    font-weight: 600;
                    font-size: 1.1rem;
                }
                
                /* Compact table styles */
                #updateProductModal .table {
                    margin-bottom: 0;
                }
                
                #updateProductModal .table th,
                #updateProductModal .table td {
                    padding: 0.5rem;
                    font-size: 0.875rem;
                }
                
                #updateProductModal .table th {
                    font-size: 0.8125rem;
                    font-weight: 600;
                }
                
                /* Styles for product info form */
                .card-header {
                    border-bottom: 1px solid #e9ecef;
                    padding: 0.75rem 1rem;
                }
                
                .card-header h6 {
                    color: #495057;
                    font-weight: 600;
                    font-size: 0.875rem;
                    margin-bottom: 0;
                }
                
                #updateProductModal .form-group {
                    margin-bottom: 0.5rem;
                }
                
                /* More compact spacing specifically for updateProductInfoForm */
                #updateProductInfoForm .form-group {
                    margin-bottom: 0.375rem;
                }
                
                #updateProductInfoForm .form-group label {
                    margin-bottom: 0.25rem;
                }
                
                /* Increased margin for category dropdown form-group */
                #updateProductInfoForm .form-group:has(.btn-group) {
                    margin-bottom: 0.5rem !important;
                    margin-top: 0.375rem !important;
                }
                
                /* Alternative selector if :has() is not supported */
                #updateProductInfoForm .form-group .btn-group {
                    margin-top: 0.25rem;
                    margin-bottom: 0;
                }
                
                /* Increased margin from category label */
                #updateProductInfoForm .form-group:has(.btn-group) label {
                    margin-bottom: 0.25rem !important;
                    margin-top: 0 !important;
                }
                
                /* Specific targeting for category form group */
                #updateProductInfoForm .form-group:nth-child(2) {
                    margin-top: 0.375rem !important;
                    margin-bottom: 0.5rem !important;
                }
                
                .btn-group .btn {
                    border-radius: 0.375rem 0 0 0.375rem;
                }
                
                .btn-group .dropdown-toggle-split {
                    border-radius: 0 0.375rem 0.375rem 0;
                }
                
                .material-form .form-control:focus + .control-label,
                .material-form .form-control:valid + .control-label {
                    top: -14px;
                    font-size: 12px;
                    color: #495057;
                }
                
                .text-right {
                    text-align: right;
                }
                
                /* Icon-only button styles */
                .btn-sm.btn-otline-dark {
                    min-width: 32px;
                    height: 32px;
                    padding: 0.25rem 0.5rem;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .btn-sm.btn-otline-dark:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    transition: all 0.2s ease;
                }
                
                .btn-sm i {
                    font-size: 14px;
                }
                
                /* Modal positioning - Right side next to update modal */
                .modal-dialog-right {
                    position: fixed;
                    left: calc(50% + 260px); /* Start position next to main modal */
                    top: 50%;
                    transform: translateY(-50%);
                    margin: 0;
                    width: 450px; /* Reasonable width - not too wide, not too narrow */
                    max-width: calc(100vw - 50% - 280px); /* Ensure it fits on screen */
                }
                
                /* Responsive positioning for medium screens */
                @media (max-width: 1400px) {
                    #updateProductModal.modal-shifted .modal-dialog {
                        transform: translateX(-150px);
                    }
                    
                    .modal-dialog-right {
                        left: calc(50% + 200px);
                        width: 400px;
                    }
                }
                
                /* Positioning for smaller large screens */
                @media (max-width: 1200px) {
                    #updateProductModal.modal-shifted .modal-dialog {
                        transform: translateX(-100px);
                    }
                    
                    .modal-dialog-right {
                        left: calc(50% + 150px);
                        width: 350px;
                    }
                }
                
                .modal-dialog-right .modal-content {
                    border-radius: 0.5rem;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                }
                
                /* Animation for right modal */
                #editBatchModal.fade .modal-dialog-right {
                    transition: transform 0.3s ease-out;
                    transform: translate(100%, -50%);
                }
                
                #editBatchModal.show .modal-dialog-right {
                    transform: translate(0, -50%);
                }
                
                /* Ensure modal backdrop doesn't interfere */
                #editBatchModal .modal-backdrop {
                    opacity: 0.3;
                }
                
                /* Responsive behavior */
                @media (max-width: 768px) {
                    .modal-dialog-right {
                        position: relative;
                        left: auto;
                        top: auto;
                        transform: none;
                        width: 100%;
                        margin: 1.75rem auto;
                    }
                    
                    #editBatchModal.fade .modal-dialog-right,
                    #editBatchModal.show .modal-dialog-right {
                        transform: none;
                    }
                }
            </style>

            @endsection

            