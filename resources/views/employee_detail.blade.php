@extends('layouts.admin')

@section('content')
    <div class="col-lg-12 grid-margin stretch-card" id="mainProductTable">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('employee.update', $user->id) }}" method="POST">
                    @csrf
                    {{-- $product tersedia karena kita berada di edit.blade.php --}}
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    
                    <div class="modal-header mb-3">
                        <h5 class="modal-title" id="addBatchModalLabel">Detail Pegawai</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Peran</label>
                            
                            <input type="hidden" name="role_id" id="selected_role_id" value="{{ $user->role->id }}" required>

                            <div class="btn-group d-block">
                                <button type="button" class="btn btn-outline-primary" id="role_dropdown_button" value="{{ $user->role->id }}">
                                    {{ $user->role->name }}
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
                        <div class="mb-3">
                            <label for="phone" class="form-label">No. Telp</label>
                            <input type="number" class="form-control" name="phone" value="{{ $user->phone }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
    </script>
@endpush