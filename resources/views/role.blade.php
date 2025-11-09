            @extends('layouts.admin')

            @section('content')
            
            {{-- SWEATALERT --}}

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
            
            {{-- SEARCH AND FILTER SECTION --}}

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="searchRole">Cari Peran</label>
                                <input type="text" class="form-control" id="searchRole" placeholder="Nama Peran">
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
            
            <div class="col-lg-12 grid-margin stretch-card" id="mainRoleTable">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Role</h4>
                        <div class="btn-wrapper">
                            @if (Auth::user()->role->name != "Cashier")
                                <button type="button" class="btn btn-primary align-items-center" data-toggle="modal" data-target="#addCategoryModal">
                                    <i class="mdi mdi-tag-plus"></i> Add New role
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Role</th>
                            <th class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr id="category-row-{{ $role->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="category-name">{{ $role->name }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm me-1 edit-role-btn"
                                        data-name="{{ $role->name }}" 
                                        data-id="{{ $role->id }}"
                                        data-url="{{ route('role.update', $role->id) }}">
                                            <i class="mdi mdi-pencil"></i> Edit Role
                                        </button>
                                        @if (Auth::user()->role->name != "Cashier")
                                            <form action="{{ route('role.destroy', $role->id) }}" method="POST" class="form-delete d-inline">
                                                @csrf
                                                @method('DELETE')
                                                
                                                <button type="submit" class="btn btn-danger btn-sm" data-name="{{ $role->name }}">
                                                    <i class="mdi mdi-delete"></i> Delete
                                                </button>
                                            </form>
                                        @endif
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


            {{-- MODAL ADD CATEGORY --}}

            <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    
                <div class="modal-dialog modal-dialog-centered" role="document">
                    
                    <div class="modal-content">

                        <form action="{{ route('role.store') }}" method="POST" class="forms-sample material-form">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addProductModalLabel">Add New Role</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="name" name="name" required="required" />
                                    <label for="name" class="control-label">New Role</label><i class="bar"></i>
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

                <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        
                        <div class="modal-content">

                            <form action="{{ route('role.update') }}" method="POST" id="editRoleForm">
                                @csrf
                                {{-- @method('PUT') --}}
                                <input type="hidden" id="role_id" name="role_id">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editRoleModalLabel">Edit Peran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="role_name" name="role_name" required="required" />
                                        <label for="name" class="control-label">Nama Kategori</label><i class="bar"></i>
                                        
                                        <div id="name_error" class="text-danger small mt-1"></div>
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

            {{-- END MODAL EDIT CATEGORY --}}

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

                // document.addEventListener('DOMContentLoaded', function() {
    
                //     // === SEARCH FUNCTIONALITY ===
                //     const searchInput = document.getElementById('searchProduct');
                //     const resultsContainer = document.getElementById('searchResultsContainer');
                //     const mainProductTable = document.getElementById('mainProductTable');

                //     // Pastikan semua elemen ada sebelum menambahkan event listener
                //     if (searchInput && resultsContainer && mainProductTable) {
                        
                //         searchInput.addEventListener('input', function() {
                //             const searchTerm = this.value.trim();
                //             console.log(searchTerm);

                //             if (searchTerm === '') {
                //                 resultsContainer.innerHTML = '';
                //                 resultsContainer.style.display = 'none';
                //                 mainProductTable.style.display = 'block';
                //                 return;
                //             }

                //             resultsContainer.style.display = 'block';
                //             mainProductTable.style.display = 'none';

                //             fetch(`/category/search?query=${encodeURIComponent(searchTerm)}`)
                //                 // 1. Kembali menggunakan .json() karena menerima data
                //                 .then(response => response.json())
                //                 .then(data => {
                //                     resultsContainer.innerHTML = ''; // Kosongkan hasil lama

                //                     // 2. Buat struktur card dan tabel secara dinamis
                //                     const resultsCard = document.createElement('div');
                //                     resultsCard.className = 'card';

                //                     let cardContent = `
                //                         <div class="card-body">
                //                             <h4 class="card-title mb-4">Hasil Pencarian untuk "${searchTerm}"</h4>
                //                             <div class="table-responsive">
                //                                 <table class="table table-hover">
                //                                     <thead>
                //                                         <tr>
                //                                             <th>No</th>
                //                                             <th>Name</th>
                //                                             <th>Action</th>
                //                                         </tr>
                //                                     </thead>
                //                                     <tbody>
                //                     `;

                //                     // 3. Cek jika ada hasil
                //                     if (data.length > 0) {
                //                         // 4. Loop setiap produk dan buat baris tabel (<tr>)
                //                         data.forEach((category, index) => {

                //                             // Buat URL untuk action edit dan delete
                //                             const editUrl = `{{ url('product') }}/${category.id}/edit`;
                //                             const deleteUrl = `{{ url('product') }}/${category.id}`;

                //                             cardContent += `
                //                                 <tr>
                //                                     <td>${index + 1}</td>
                //                                     <td>${category.name}</td>
                //                                     <td>
                //                                         <a href="${editUrl}" class="btn btn-warning btn-sm me-1">
                //                                             <i class="mdi mdi-pencil"></i> Edit / Lihat Batch
                //                                         </a>
                //                                         <form action="${deleteUrl}" method="POST" class="form-delete d-inline" onsubmit="handleDelete(event)">
                //                                             @csrf
                //                                             @method('DELETE')
                //                                             <button type="submit" class="btn btn-danger btn-sm" data-name="${category.name}">
                //                                                 <i class="mdi mdi-delete"></i> Delete
                //                                             </button>
                //                                         </form>
                //                                     </td>
                //                                 </tr>
                //                             `;
                //                         });
                //                     } else {
                //                         // Jika tidak ada hasil
                //                         cardContent += `
                //                             <tr>
                //                                 <td colspan="6" class="text-center">Produk tidak ditemukan.</td>
                //                             </tr>
                //                         `;
                //                     }

                //                     // 5. Tutup tag html
                //                     cardContent += `
                //                                     </tbody>
                //                                 </table>
                //                             </div>
                //                         </div>
                //                     `;

                //                     // 6. Masukkan semua HTML yang sudah jadi ke dalam card dan tampilkan
                //                     resultsCard.innerHTML = cardContent;
                //                     resultsContainer.appendChild(resultsCard);
                //                 })
                //                 .catch(error => {
                //                     console.error('Error fetching search results:', error);
                //                 });
                //         });

                //     } else {
                //         // Jika salah satu elemen tidak ditemukan, log error ke console
                //         console.error('Satu atau lebih elemen untuk fungsionalitas pencarian tidak ditemukan!');
                //     }

                // });

                $(document).ready(function () {

                    //Fungsi Search Product
                    const searchInput = $('#searchRole');
                    const resultsContainer = $('#searchResultsContainer');
                    const mainRoleTable = $('#mainRoleTable');

                    if (searchInput.length > 0 && resultsContainer.length > 0 && mainRoleTable.length > 0) {
                        searchInput.on('input', function() {
                            const searchTerm = $(this).val().trim();

                            if (searchTerm === '') {
                                resultsContainer.html('');
                                resultsContainer.hide();
                                mainRoleTable.show();
                                return;
                            }

                            resultsContainer.show();
                            mainRoleTable.hide();

                            $.ajax({
                                url: "{{ route('role.search') }}", // Sudah benar
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
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                    `;

                                    const csrfToken = $('meta[name="csrf-token"]').attr('content');

                                    if (data.length > 0) {
                                        $.each(data, function(index, role) {

                                            const deleteUrl = `/product/${role.id}`;
                                            console.log(role.name);
                                            console.log(role.id);
                                            
                                            cardContent += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${role.name}</td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm me-1 edit-role-btn" 
                                                        data-name="${role.name}" 
                                                        data-id="${role.id}"
                                                            <i class="mdi mdi-pencil"></i> Edit Peran
                                                        </button>

                                                        <form action="${deleteUrl}" method="POST" class="form-delete d-inline">
                                                            <input type="hidden" name="_token" value="${csrfToken}">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="btn btn-danger btn-sm" data-name="${role.name}">
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
                });

                $(document).ready(function () {

                    // 1. Menangani event klik pada tombol edit
                    $(document).on('click', '.edit-role-btn', function () {
                        // Ambil data dari tombol yang di-klik
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let url = $(this).data('url');

                        // Isi value input nama kategori dengan nama yang sekarang
                        $('#editRoleForm #role_id').val(id);
                        $('#editRoleForm #role_name').val(name);
                        // console.log(name);
                        // console.log(id);
                        // console.log(url);
                        // Hapus pesan error sebelumnya (jika ada)
                        $('#editRoleForm #role_name').removeClass('is-invalid');
                        $('#editRoleForm #name_error').text('');

                        // [INI KUNCINYA] Tampilkan modal via JavaScript
                        $('#editRoleModal').modal('show');

                    });

                });

            </script>
            @endpush

            