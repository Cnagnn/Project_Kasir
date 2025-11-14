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

{{-- SEARCH AND FILTER SECTION --}}

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="searchEmployee">Cari Karyawan</label>
                    <input type="text" class="form-control" id="searchEmployee" placeholder="Nama Karyawan">
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
<div class="col-lg-12 grid-margin stretch-card" id="mainEmployeeTable">
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
                            <button class="btn btn-warning btn-sm me-1 edit-employee-btn"
                            data-employeeid = "{{ $user->id }}"
                            data-employeename = "{{ $user->name }}"
                            data-roleid = "{{ $user->role->id }}"
                            data-rolename = "{{ $user->role->name }}"
                            data-url="{{ route('employee.update', $user->id) }}">
                                <i class="mdi mdi-pencil"></i> Edit Karyawan
                            </button>
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
                        <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
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
                        <button type="button" class="btn btn-light modal-close-btn" data-dismiss="modal">Batal</button>
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
                        <h5 class="modal-title" id="addRoleModalLabel">Tambah Peran Baru</h5>
                        <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
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
                        <button type="button" class="btn btn-light modal-close-btn" data-dismiss="modal">Batal</button>
                        <button type="submit" class="button btn btn-primary"><span>Simpan</span></button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- END MODAL ADD ROLE --}}

    {{-- MODAL EDIT PRODUCT --}}

    <div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form action="{{ route('employee.update') }}" method="POST" class="forms-sample material-form" id="editEmployeeForm">
                    @csrf
                    <input type="hidden" id="employee_id" name="employee_id">
                    {{-- <input type="hidden" id="category_id" name="categoryId"> --}}
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmployeeModalLabel">Edit Product</h5>
                        <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" class="form-control" id="employee_name" name="employee_name" required="required" />
                            <label for="name" class="control-label">Nama Karyawan</label><i class="bar"></i>
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
                                    @foreach ($role as $role)
                                        <li>
                                            <a class="dropdown-item" href="#" data-id="{{ $role->id }}" data-name="{{ $role->name }}">
                                                {{ $role->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <small class="form-text text-muted">Klik panah untuk memilih kategori.</small>
                        </div>
                        </div> 

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light modal-close-btn" data-dismiss="modal">Batal</button>
                        <button type="submit" class="button btn btn-primary" id="btnSaveEmployeeEdit"><span>Simpan</span></button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- END MODAL EDIT PRODUCT --}}

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

        $(document).ready(function () {

            //Fungsi Search Product
            const searchInput = $('#searchEmployee');
            const resultsContainer = $('#searchResultsContainer');
            const mainEmployeeTable = $('#mainEmployeeTable');

            if (searchInput.length > 0 && resultsContainer.length > 0 && mainEmployeeTable.length > 0) {
                searchInput.on('input', function() {
                    const searchTerm = $(this).val().trim();

                    if (searchTerm === '') {
                        resultsContainer.html('');
                        resultsContainer.hide();
                        mainEmployeeTable.show();
                        return;
                    }

                    resultsContainer.show();
                    mainEmployeeTable.hide();

                    $.ajax({
                        url: "{{ route('employee.search') }}", // Sudah benar
                        type: 'GET',
                        data: { query: searchTerm },
                        dataType: 'json',
                        success: function(data) {
                            resultsContainer.html(''); 
                            const resultsCard = $('<div class="card"></div>');
                        
                            // console.log(data);
                            
                            let cardContent = `
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Hasil Pencarian untuk "${searchTerm}"</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>Role</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;

                            const csrfToken = $('meta[name="csrf-token"]').attr('content');

                            if (data.length > 0) {
                                $.each(data, function(index, employee) {

                                    const deleteUrl = `/product/${employee.id}`;
                                    console.log(employee.name);
                                    console.log(employee.role.id);
                                    
                                    cardContent += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${employee.name}</td>
                                            <td>${employee.role.name}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm me-1 edit-employee-btn" 
                                                    data-employeeid="${employee.id}"
                                                    data-employeename="${employee.name}"
                                                    data-roleid="${employee.role.id}" 
                                                    data-rolename="${employee.role.name}">
                                                    <i class="mdi mdi-pencil"></i> Edit Karyawan
                                                </button>

                                                <form action="${deleteUrl}" method="POST" class="form-delete d-inline">
                                                    <input type="hidden" name="_token" value="${csrfToken}">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-danger btn-sm" data-name="${employee.name}">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    `;
                                });
                            } else {
                                cardContent += `<tr><td colspan="6" class="text-center">Produk tidak ditemukan.</td></tr>`;
                            }

                            cardContent += `</tbody></table></div></div>`;
                            resultsCard.html(cardContent);
                            resultsContainer.append(resultsCard);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching search results:', error);
                            resultsContainer.html('<div class="alert alert-danger">Gagal memuat hasil pencarian.</div>');
                        }
                    });
                });
            }
            //Fungsi Search Product

            //Menangani event klik pada tombol edit

            let originalRoleName = '';
            let originalEmployeeName = '';
            let $saveButton = $('#btnSaveEmployeeEdit');

            $(document).on('click', '.edit-employee-btn', function () {
                // Ambil data dari tombol yang di-klik
                let employeeId = $(this).data('employeeid');
                let employeeName = $(this).data('employeename');
                let roleId = $(this).data('roleid');
                let roleName = $(this).data('rolename');

                console.log(roleId);

                // Isi value input nama kategori dengan nama yang sekarang
                $('#editEmployeeForm #employee_id').val(employeeId);
                $('#editEmployeeForm #employee_name').val(employeeName);
                $('#editEmployeeForm #selected_role_id').val(roleId);
                $('#editEmployeeForm #role_dropdown_button').text(roleName);

                originalEmployeeName = employeeName;
                originalRoleName = roleName;
                
                // Hapus pesan error sebelumnya (jika ada)
                $('#editEmployeeForm #employee_name').removeClass('is-invalid');
                $('#editEmployeeForm #name_error').text('');

                // Langsung disable tombol simpan saat modal dibuka
                $saveButton.prop('disabled', true);

                // [INI KUNCINYA] Tampilkan modal via JavaScript
                $('#editEmployeeModal').modal('show');

            });

            // 1. Menangani event klik pada tombol X edit modal
            $(document).on('click', '.modal-close-btn', function () {
            
                $('#editEmployeeModal').modal('hide');

            });

            // Event listener untuk input nama kategori
            // Ini akan memantau setiap ketikan di field #category_name
            $('#editEmployeeForm #employee_name').on('input', function () {
                let currentValue = $(this).val(); // Dapatkan nilai saat ini
                
                // Bandingkan nilai input saat ini dengan nama asli
                // Jika sama, tombol disabled (true). Jika beda, tombol enabled (false).
                $saveButton.prop('disabled', currentValue === originalEmployeeName);
            });

            $(document).on('click', '#editEmployeeModal #role_options .dropdown-item', function(e) {
                e.preventDefault();

                const selectedRoleId = $(this).data('id');
                const selectedRoleName = $(this).data('name');

                // console.log(selectedRoleId);

                // Update teks tombol dropdown & input hidden
                $('#editEmployeeForm #role_dropdown_button').text(selectedRoleName);
                $('#editEmployeeForm #selected_role_id').val(selectedRoleId);

                // Aktifkan tombol simpan karena ada perubahan
                $('#btnSaveEmployeeEdit').prop('disabled', false);
            });

            // Reset tombol saat modal ditutup (baik via tombol X, Batal, atau klik luar)
            $('#editEmployeeForm').on('hidden.bs.modal', function () {
                $saveButton.prop('disabled', true); // Selalu nonaktifkan saat ditutup
                originalEmployeeName = ''; // Kosongkan variabel
            });
            //Menangani event klik pada tombol edit


            
            
        });
    </script>
@endpush
