@extends('layouts.admin')

@section('content')

{{-- SWEATALERT --}}

@if(session()->has('success'))
    <script>
        Swal.fire({
            title: "BERHASIL",
            text: "{{ session('success') }}",
            icon: "success"
        });
    </script>    
@endif


{{-- END SWEATALERT --}}

{{-- MAIN TABLE / PRODUCT LIST --}}
<div class="col-lg-12 grid-margin stretch-card" id="mainProductTable">
    <div class="card">
        <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title mb-0">Daftar Pegawai</h4>
            <div class="btn-wrapper">
                @if (Auth::user()->role->name != "Cashier")
                    <button type="button" class="btn btn-outline-primary me-0" data-toggle="modal" data-target="#addRoleModal">
                        <i class="mdi mdi-plus"></i> Tambah Peran
                    </button>
                    <button type="button" class="btn btn-outline-primary me-0" data-toggle="modal" data-target="#addEmployeeModal">
                        <i class="mdi mdi-plus"></i> Tambah Pegawai
                    </button>
                @endif
            </div>
        </div>
        <div class="table-responsive">
             <table class="table table-bordered table-hover">
            <thead>
                <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Peran</th>
                <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->role->name }}</td>
                        <td>
                            <a href="{{ route('employee.detail', $user->id) }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-information-outline"></i> Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
{{-- MAIN TABLE / PRODUCT LIST --}}



@if (Auth::user()->role->name != "Cashier")

    {{-- MODAL ADD USER --}}
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            
            <div class="modal-content">

                <form action="{{ route('employee.store') }}" method="POST" class="forms-sample material-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEmployeeModalLabel">Tambah Pegawai Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="card-description">Isi detail Pegawai di bawah ini.</p>

                        <div class="form-group">
                            <input type="text" class="form-control" id="name" name="name" required="required" />
                            <label for="name" class="control-label">Nama Pegawai</label><i class="bar"></i>
                        </div>

                        <div class="form-group">
                            <label>Peran</label>
                            
                            <input type="hidden" name="role_id" id="selected_role_id" required>

                            <div class="btn-group d-block">
                                <button type="button" class="btn btn-outline-primary" id="role_dropdown_button">
                                    -- Pilih Peran --
                                </button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                
                                <ul class="dropdown-menu" id="role_options">
                                    {{-- Loop untuk menampilkan semua kategori yang tersedia --}}
                                    @foreach ($role as $item)
                                        <li>
                                            <a class="dropdown-item" href="#" data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                                {{ $item->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <small class="form-text text-muted">Klik panah untuk memilih peran.</small>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="email" name="email" required="required" />
                            <label for="email" class="control-label">Email</label><i class="bar"></i>
                        </div>

                        <div class="form-group">
                            <input type="number" class="form-control" id="phone" name="phone" required="required" />
                            <label for="phone" class="control-label">No. Telp</label><i class="bar"></i>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="password" name="password" required="required" />
                            <label for="password" class="control-label">Password</label><i class="bar"></i>
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
    {{-- END MODAL ADD USER --}}


    {{-- MODAL ADD ROLE --}}
    <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            
            <div class="modal-content">

                <form action="{{ route('role.store') }}" method="POST" class="forms-sample material-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Tambah Peran Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" name="name" required="required" />
                            <label for="name" class="control-label">Nama Peran</label><i class="bar"></i>
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
    {{-- END MODAL ADD ROLE --}}
@endif

@endsection

@push('scripts')
    <script>
        // {{-- DOM TOMBOL KATEGORI --}}
        document.addEventListener('DOMContentLoaded', function () {
            // Cari semua item role di dalam dropdown add user
            const roleItems = document.querySelectorAll('#role_options .dropdown-item');
            
            // Cari tombol utama dan input tersembunyi add user
            const dropdownButton = document.getElementById('role_dropdown_button');
            const hiddenInput = document.getElementById('selected_role_id');

            // Tambahkan event listener untuk setiap item kategori add user
            roleItems.forEach(item => {
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
        });
        // {{-- END DOM TOMBOL KATEGORI --}}

        // === UPDATE PRODUCT MODAL ===
        document.addEventListener('DOMContentLoaded', function () {
            
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
        // === END UPDATE PRODUCT MODAL ===

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
