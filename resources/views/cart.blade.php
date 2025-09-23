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

{{-- END SWEATALERT --}}

{{-- MAIN TABLE / PRODUCT LIST --}}

<div class="col-lg-12 grid-margin stretch-card" id="mainProductTable">
    <div class="card">
        <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title mb-0">Daftar Belanjaan</h4>
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
                <th>kuantitas</th>
                <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if (session('cart'))
                {{-- {{ dd($carts) }} --}}
                    @foreach (session('cart') as $item)
                        <tr id="cart-row-{{ $item['id'] }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['category'] }}</td>
                            <td>{{ $item['stock'] }}</td>
                            <td>{{ $item['sell_price'] }}</td>
                            <td id="quantity-{{ $item['id'] }}">{{ $item['quantity'] }}</td>
                            <td>
                                <button class="btn btn-primary btn-increase-qty" data-id="{{ $item['id'] }}">
                                    <i class="fas fa-shopping-cart"></i> Tambah
                                </button>
                                <button class="btn btn-primary btn-decrease-qty" data-id="{{ $item['id'] }}">
                                    <i class="fas fa-shopping-cart"></i> Kurangi
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data produk.</td>
                    </tr>
                @endif
            </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

{{-- MAIN TABLE / PRODUCT LIST --}}

@endsection


@push('scripts')
<script>
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        // Gunakan event delegation agar tombol yang baru dimuat (misal via live search) tetap berfungsi
        $(document).on('click', '.btn-increase-qty', function(e) {
            console.log('Tombol .btn-increase-qty berhasil diklik!');    
            e.preventDefault(); // Mencegah aksi default dari tombol

            var productId = $(this).data('id'); // Ambil product_id dari atribut data-id
            var button = $(this); // Simpan referensi tombol
            console.log(productId);
            // Kirim request AJAX ke server
            $.ajax({
                url: "{{ route('cart.increaseQtyCart') }}", // URL ke controller
                method: "POST",
                data: {
                    product_id: productId
                },
                // Aksi jika request berhasil
                success: function(response) {
                    console.log(response); // Untuk debug

                    // Jika request berhasil, update tampilan kuantitas
                    if (response.success) {
                        $('#quantity-' + productId).text(response.new_quantity);
                    }

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
    })

    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        // Gunakan event delegation agar tombol yang baru dimuat (misal via live search) tetap berfungsi
        $(document).on('click', '.btn-decrease-qty', function(e) {
            console.log('Tombol .btn-decrease-qty berhasil diklik!');    
            e.preventDefault(); // Mencegah aksi default dari tombol

            var productId = $(this).data('id'); // Ambil product_id dari atribut data-id
            var button = $(this); // Simpan referensi tombol
            console.log(productId);
            // Kirim request AJAX ke server
            $.ajax({
                url: "{{ route('cart.decreaseQtyCart') }}", // URL ke controller
                method: "POST",
                data: {
                    product_id: productId
                },
                // Aksi jika request berhasil
                success: function(response) {
                    console.log(response); // Untuk debug

                    // Jika request berhasil, update tampilan kuantitas
                    if (response.success) {
                        $('#quantity-' + productId).text(response.new_quantity);
                    }
                    if (response.unset_item){
                        $('#cart-row-' + productId).fadeOut(300, function() {
                            $(this).remove(); 
                        });
                    }

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
    })
</script>
@endpush
