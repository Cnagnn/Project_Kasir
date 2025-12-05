@extends('layouts.admin')

@section('page-title', 'Kategori Produk')
@section('page-description', 'Kelola kategori produk untuk mengorganisir stok barang')

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
                .table-centered th,
                .table-centered td {
                    text-align: center;
                    vertical-align: middle;
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
            </style>

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
            @if(session()->has('category_destroy_success'))
                <script>
                    Swal.fire({
                        title: "BERHASIL",
                        text: "{{ session('category_destroy_success') }}",
                        icon: "success"
                    });
                </script>    
            @endif
            @if(session()->has('category_destroy_failed'))
                <script>
                    Swal.fire({
                        title: "GAGAL",
                        text: "{{ session('category_destroy_failed') }}",
                        icon: "error"
                    });
                </script>    
            @endif
            @if(session()->has('success'))
                <script>
                    Swal.fire({
                        title: "BERHASIL",
                        text: "{{ session('success') }}",
                        icon: "success"
                    });
                </script>    
            @endif
            @if(session()->has('failed'))
                <script>
                    Swal.fire({
                        title: "GAGAL",
                        text: "{{ session('failed') }}",
                        icon: "error"
                    });
                </script>    
            @endif

{{-- END SWEATALERT --}}

<div class="row">
    <div class="col-sm-12">

        {{-- SEARCH AND FILTER SECTION --}}

        <div class="col-lg-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="searchProduct">Cari Kategori</label>
                                <input type="text" class="form-control" id="searchProduct" placeholder="Nama Kategori">
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
                        <h4 class="card-title mb-0">Daftar Kategori</h4>
                        <div class="btn-wrapper">
                            @if (Auth::user()->role->name != "Cashier")
                                <button type="button" class="btn btn-primary align-items-center" data-toggle="modal" data-target="#addCategoryModal">
                                    <i class="mdi mdi-tag-plus"></i> Tambah Kategori
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover table-centered">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr id="category-row-{{ $category->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="category-name">{{ $category->name }}</td>
                                    @if ($category->is_archived == "yes")
                                        <td>Archived</td>
                                    @else
                                        <td>Active</td>
                                    @endif
                                    <td>
                                        <div class="action-btn-group" role="group" aria-label="Aksi kategori">
                                            <button 
                                                type="button"
                                                class="btn btn-primary btn-sm edit-category-btn"
                                                data-name="{{ $category->name }}" 
                                                data-id="{{ $category->id }}"
                                                data-url="{{ route('category.update', $category->id) }}">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            @if (Auth::user()->role->name != "Cashier")
                                                <form action="{{ route('category.destroy', $category->id) }}" method="POST" class="form-delete d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-primary btn-sm" data-name="{{ $category->name }}">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('category.archive', $category->id) }}" method="POST" class="form-archive d-inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm" data-name="{{ $category->name }}">
                                                    <i class="mdi mdi-archive"></i>
                                                </button>
                                            </form>
                                        </div>
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
                    <div class="card-body mt-3 d-flex justify-content-center">
                        {{ $categories->links() }}
                    </div>
                  </div>
                </div>
            </div>

            {{-- MAIN TABLE / PRODUCT LIST --}}


            {{-- MODAL ADD CATEGORY --}}

            <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    
                <div class="modal-dialog modal-dialog-centered" role="document">
                    
                    <div class="modal-content">

                        <form action="{{ route('category.store') }}" method="POST" class="forms-sample material-form">
                            @csrf
                            <input type="hidden" value="category_page" name="page">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addProductModalLabel">Tambah Kategori Baru</h5>
                                <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
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

            {{-- MODAL EDIT CATEGORY --}}

                <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
        
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        
                        <div class="modal-content">

                            <form action="{{ route('category.update') }}" method="POST" id="editCategoryForm">
                                @csrf
                                <input type="hidden" id="category_id" name="id">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addProductModalLabel">Edit Kategori</h5>
                                    <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Nama Kategori</label><i class="bar"></i>
                                        <input type="text" class="form-control" id="category_name" name="name" required="required" />
                                        <div id="name_error" class="text-danger small mt-1"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light modal-cancel-btn" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary" id="btnSaveCategoryEdit">Simpan</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

        {{-- END MODAL EDIT CATEGORY --}}

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

                // === HANDLE ARCHIVE BUTTON CLICK ===
                // Pastikan CDN SweetAlert sudah dimuat
                document.addEventListener('DOMContentLoaded', function () {
                    
                    // Cari SEMUA form yang punya class .form-delete
                    const deleteForms = document.querySelectorAll('.form-archive');
                    
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
                                text: `Anda akan Meng-arsipkan "${productName}".`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Ya, Arsipkan!',
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

                            fetch(`/category/search?query=${encodeURIComponent(searchTerm)}`)
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
                                                            <th>Nama</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                    `;

                                    // 3. Cek jika ada hasil
                                    if (data.length > 0) {
                                        // 4. Loop setiap produk dan buat baris tabel (<tr>)
                                        data.forEach((category, index) => {

                                            // Buat URL untuk action edit dan delete
                                            const editUrl = `{{ url('product') }}/${category.id}/edit`;
                                            const deleteUrl = `{{ url('product') }}/${category.id}`;

                                            cardContent += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${category.name}</td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm me-1 edit-category-btn"
                                                        data-name="${category.name}" 
                                                        data-id="${category.id}"
                                                            <i class="mdi mdi-pencil"></i> Edit Kategori
                                                        </button>
                                                        <form action="${deleteUrl}" method="POST" class="form-delete d-inline" onsubmit="handleDelete(event)">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" data-name="${category.name}">
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

                $(document).ready(function () {

                    let originalCategoryName = '';
                    let $saveButton = $('#btnSaveCategoryEdit'); // Tombol simpan dari modal

                    // 1. Menangani event klik pada tombol edit untuk menampilkan modal edit category
                    $(document).on('click', '.edit-category-btn', function () {
                        // Ambil data dari tombol yang di-klik
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let url = $(this).data('url');

                        // Simpan nama asli ke variabel di scope atas
                        originalCategoryName = name;

                        // Set action form di modal sesuai dengan URL update
                        $('#editCategoryForm').attr('action', url);
                        // Isi value input nama kategori dengan nama yang sekarang
                        $('#editCategoryForm #category_id').val(id);
                        $('#editCategoryForm #category_name').val(name);
                        // console.log(name);
                        // console.log(id);
                        // console.log(url);
                        // Hapus pesan error sebelumnya (jika ada)
                        $('#editCategoryForm #category_name').removeClass('is-invalid');
                        $('#editCategoryForm #name_error').text('');

                        // Langsung disable tombol simpan saat modal dibuka
                        $saveButton.prop('disabled', true);

                        // Tampilkan modal via JavaScript
                        $('#editCategoryModal').modal('show');
                    });

                    // 2. Menangani event klik pada tombol X modal edit category
                    $(document).on('click', '.modal-close-btn', function () {
                        $('#editCategoryModal').modal('hide');
                    });

                    // 3. Menangani event klik pada tombol cancel modal edit category
                    $(document).on('click', '.modal-cancel-btn', function () {
                        $('#editCategoryModal').modal('hide');
                    });

                    // Event listener untuk input nama kategori
                    // Ini akan memantau setiap ketikan di field #category_name
                    $('#editCategoryForm #category_name').on('input', function () {
                        let currentValue = $(this).val(); // Dapatkan nilai saat ini
                        
                        // Bandingkan nilai input saat ini dengan nama asli
                        // Jika sama, tombol disabled (true). Jika beda, tombol enabled (false).
                        $saveButton.prop('disabled', currentValue === originalCategoryName);
                    });

                    // Reset tombol saat modal ditutup (baik via tombol X, Batal, atau klik luar)
                    $('#editCategoryModal').on('hidden.bs.modal', function () {
                        $saveButton.prop('disabled', true); // Selalu nonaktifkan saat ditutup
                        originalCategoryName = ''; // Kosongkan variabel
                    });

                });

            </script>
            @endpush

            