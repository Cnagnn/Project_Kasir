@extends('layouts.admin')

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

@php
    $cart = Session::get('cart');
    // dd($cart);
    $total_payment = 0;
@endphp

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
            <table class="table table-hover">
            <thead>
                <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Kuantitas</th>
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
                            @php
                                $total_payment = $total_payment + ($item['sell_price'] * $item['quantity']);
                            @endphp
                            <td>
                                <button class="btn btn-primary btn-increase-qty btn-sm" data-id="{{ $item['id'] }}">
                                    <i class="mdi mdi-plus"></i> Tambah
                                </button>
                                <button class="btn btn-primary btn-decrease-qty btn-sm" data-id="{{ $item['id'] }}">
                                    <i class=" mdi mdi-minus"></i> Kurangi
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
        <div class="col-lg-12 grid-margin stretch-card">
                <div class="card card-rounded">
                    <div class="card-body text-end">
                        @if (session('cart'))
                            <button class="btn btn-danger me-2" data-toggle="modal" data-target="#btnCheckout">
                                Checkout
                            </button>
                        @endif
                        
                        {{-- <button type="submit" form="product-form" class="btn btn-primary">Update Data Produk</button> --}}
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

{{-- MAIN TABLE / PRODUCT LIST --}}

<div class="modal fade" id="btnCheckout" tabindex="-1" role="dialog" aria-labelledby="btnCheckoutLabel" aria-hidden="true">
        
    <div class="modal-dialog modal-dialog-centered" role="document">
        
        <div class="modal-content">

            <form action="{{ route('product.store') }}" method="POST" class="forms-sample material-form">
                @csrf
                <input type="hidden" value="product_page" name="page">
                <div class="modal-header">
                    <h5 class="modal-title" id="btnCheckoutLabel">Checkout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- <p class="card-description">Isi detail produk di bawah ini.</p> --}}
                    
                    @php
                        
                    @endphp

                    <div class="form-group">
                        <strong id="totalPrice" data-total="{{ $total_payment }}">
                            Rp {{ number_format($total_payment, 0, ',', '.') }}
                        </strong>
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

    </div>
</div>

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
                        $('#totalPrice').text(response.total_transaction_price);
                    }

                },
                // Aksi jika request gagal
                error: function(xhr, status, error) {
                    console.error("Terjadi kesalahan: " + error);
                    console.error(response);
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
                        $('#totalPrice').text(response.total_transaction_price);
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
