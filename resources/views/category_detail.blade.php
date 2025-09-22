            @extends('layouts.admin')

            @section('content')
            
            {{-- SWEATALERT --}}

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
            @if(session()->has('category_update_success'))
                <script>
                    Swal.fire({
                        title: "BERHASIL",
                        text: "{{ session('category_update_success') }}",
                        icon: "success"
                    });
                </script>    
            @endif

            {{-- END SWEATALERT --}}
            
            {{-- SEARCH AND FILTER SECTION --}}

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Info Kategori</h4>
                        {{-- Form ini akan mengarah ke ProductController@update --}}
                        <form id="category-form" action="{{ route('category.update', $categoryId) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <input type="hidden" name="id" value="{{ $categoryId }}">
                            </div>

                            <div class="mb-3">
                                <label for="product_name" class="form-label">Nama Kategori</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="product_name" name="product_name" value="{{ old('name', $categoryName) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </form>
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
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        {{-- <h4 class="card-title mb-0">List Products of Category : {{ $categoryName }}</h4> --}}
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="searchProduct">Cari Produk</label>
                                <input type="text" class="form-control" id="searchProduct" placeholder="Nama Produk">
                            </div>
                        </div>
                        <div class="btn-wrapper">
                            <button type="button" class="btn btn-primary text-white me-0" data-toggle="modal" data-target="#addProductModal">
                                <i class="mdi mdi-plus"></i> Add Product
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th class="text-center">Aksi</th>
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
                                        <a href="{{ route('category.productDetail', $product->id) }}" class="btn btn-warning btn-sm me-1">
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

            {{-- MAIN TABLE / PRODUCT LIST --}}

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-end">
                        <a href="{{ route('category.index') }}" class="btn btn-danger me-2">
                            Kembali
                        </a>
                        <button type="submit" form="category-form" class="btn btn-primary">Update Data Produk</button>
                    </div>
                </div>
            </div>

            {{-- MODAL ADD PRODUCT --}}

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

                    dropdownButton.textContent = '{{ $categoryName }}';
                    hiddenInput.value = {{ $categoryId  }};

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
                                    resultsCard.className = 'card';

                                    let cardContent = `
                                        <div class="card-body">
                                            <h4 class="card-title mb-4">Hasil Pencarian untuk "${searchTerm}"</h4>
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
                                    `;

                                    // 3. Cek jika ada hasil
                                    if (data.length > 0) {
                                        // 4. Loop setiap produk dan buat baris tabel (<tr>)
                                        data.forEach((product, index) => {
                                            const categoryName = product.category ? product.category.name : 'Tidak ada kategori';
                                            const totalStock = product.stock_batches.reduce((sum, batch) => sum + batch.remaining_stock, 0);
                                            const lastBatch = product.stock_batches[product.stock_batches.length - 1];
                                            const sellPrice = lastBatch ? lastBatch.sell_price : 0;
                                            
                                            // Format harga ke Rupiah
                                            const formattedPrice = new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                minimumFractionDigits: 0
                                            }).format(sellPrice);

                                            // Buat URL untuk action edit dan delete
                                            const editUrl = `{{ url('product') }}/${product.id}/edit`;
                                            const deleteUrl = `{{ url('product') }}/${product.id}`;

                                            cardContent += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${product.name}</td>
                                                    <td>${categoryName}</td>
                                                    <td>${totalStock}</td>
                                                    <td>${formattedPrice}</td>
                                                    <td>
                                                        <a href="${editUrl}" class="btn btn-warning btn-sm me-1">
                                                            <i class="mdi mdi-pencil"></i> Edit / Lihat Batch
                                                        </a>
                                                        <form action="${deleteUrl}" method="POST" class="form-delete d-inline" onsubmit="handleDelete(event)">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" data-name="${product.name}">
                                                                <i class="mdi mdi-delete"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            `;
                                        });
                                    } else {
                                        // Jika tidak ada hasil
                                        cardContent += `
                                            <tr>
                                                <td colspan="6" class="text-center">Produk tidak ditemukan.</td>
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

            </script>
            @endpush

            