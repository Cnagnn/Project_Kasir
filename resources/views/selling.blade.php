@extends('layouts.admin')

@section('content')

    @if(session()->has('success'))
        <script>
            Swal.fire({
                title: "BERHASIL",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>     
    @endif
    @if(session()->has('error'))
        <script>
            Swal.fire({
                title: "GAGAL",
                text: "{{ session('error') }}",
                icon: "error"
            });
        </script>     
    @endif

    {{-- SEARCH AND FILTER SECTION --}}
    <div class="col-lg-12 grid-margin stretch-card" id="search-card">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    {{-- Kolom Pencarian --}}
                    <div class="col-md-8">
                        <div class="form-group mb-0">
                            <label for="searchProduct">Cari Produk</label>
                            <input type="text" class="form-control" id="searchProduct" placeholder="Ketik nama produk...">
                        </div>
                    </div>
                    
                    {{-- Kolom Tombol Navigasi --}}
                    <div class="col-md-4 text-end mt-4 md-0">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="btn-show-products">
                                <i class="mdi mdi-package-variant"></i> Product
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btn-show-cart">
                                <i class="mdi mdi-cart"></i> Cart
                            </button>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END SEARCH AND FILTER SECTION --}}

    {{-- RESULT SEARCH PRODUCT BOX --}}

    <div class="col-lg-12" id="searchResultsContainer">
        
    </div>

    {{-- END RESULT SEARCH PRODUCT BOX --}}

        <div class="col-lg-12 grid-margin stretch-card" id="mainProductTable">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Daftar Produk</h4>
                </div>
                <div class="container-fluid" id="product-list-container">
                    <div class="row justify-content-center">
                        @foreach ($products as $item)
                            @if ($item->stock->sum('remaining_stock') == 0)
                                @continue
                            @endif
                                <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                                    <div class="card h-100" style="border: 1px solid black"> 
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">{{ $item->name }}</h5>
                                            <h6 class="card-subtitle mb-2">
                                                Harga : <strong>{{ $item->sell_price ?? 'Data tidak tersedia' }}</strong>
                                            </h6>
                                            <p class="card-subtitle">Stok : <strong>{{ $item->stock->sum('remaining_stock') }}</strong></p>
                                            <div class="mt-auto"> 
                                                <div class="btn-wrapper">
                                                    <button type="button" class="btn btn-primary align-items-center add-to-cart-btn" data-product-id="{{ $item->id }}">
                                                        <i class="mdi mdi-cart"></i> Tambah Ke Keranjang
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                        <div class="card-body mt-3 d-flex justify-content-center">
                            {{ $products->links() }}
                        </div>
                    </div>
                     
                </div>

                <div id="cart-container" class="d-none">
                    <div class="text-center">
                        <p>Memuat data keranjang...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Proses Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('cart.checkout.process') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label">Total Belanja</label>
                            <input type="text" class="form-control form-control-lg" id="modal-total-display" readonly>
                            <input type="hidden" id="modal-total-hidden" value="0">
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Metode Pembayaran</label>
                            <select name="payment_method" id="payment-method-select" class="form-select" required>
                                <option value="Tunai" selected>Tunai (Cash)</option>
                            </select>
                        </div>

                        <div id="cash-payment-section">
                            <div class="mb-3">
                                <label for="amount-paid-display" class="form-label">Jumlah Uang Dibayar</label>
                                
                                {{-- Kita gunakan Input Group Bootstrap --}}
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    
                                    <input type="text" 
                                        id="amount-paid-display" 
                                        class="form-control" 
                                        placeholder="Masukkan jumlah uang..." 
                                        autocomplete="off"
                                    >

                                    <input type="hidden" 
                                        name="amount_paid" 
                                        id="amount-paid-hidden"
                                        required
                                    >
                                </div>
                            </div>
                            
                            <h4 id="change-display" class="text-success">Kembalian: Rp 0</h4>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Proses Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <style>
        /* Sembunyikan panah spinner di input kuantitas */
        .qty-input {
        /* Untuk browser Firefox */
        -moz-appearance: textfield;
        }

        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button {
        /* Untuk browser Chrome, Safari, Edge, Opera */
        -webkit-appearance: none;
        margin: 0;
        }
    </style>
    <script>
        $(document).ready(function () {
            
            // =======================================================
            // ==      DEFINISI ELEMEN UTAMA (KONSTANTA)          ==
            // =======================================================
            // Kontrol Tab
            const btnShowProducts = $('#btn-show-products');
            const btnShowCart = $('#btn-show-cart');
            
            // Kontainer Panel
            const productContainer = $('#product-list-container');
            const cartContainer = $('#cart-container');
            const mainProductCard = $('#mainProductTable'); // Kartu utama (produk & keranjang)
            
            // Kontrol Search
            const searchCard = $('#search-card'); // Kartu yang berisi input search
            const searchInput = $('#searchProduct'); // Input text search
            const resultsContainer = $('#searchResultsContainer'); // Div untuk hasil search

            // Variabel Modal Edit (dari kode Anda)
            let originalProductName = '';
            let originalCategoryName = '';
            let $saveButton = $('#btnSaveProductEdit'); // Pastikan ID ini ada di modal edit Anda

            // =======================================================
            // ==     LOGIKA TOMBOL PRODUK & KERANJANG (TAB)        ==
            // =======================================================
            
            btnShowProducts.on('click', function() {
                $(this).addClass('active');
                btnShowCart.removeClass('active');
                
                // Cek apakah kolom pencarian ada isinya?
                if (searchInput.val().trim() !== '') {
                    // KASUS 1: Sedang mencari produk
                    resultsContainer.show();      // Tampilkan hasil cari
                    mainProductCard.hide();       // Sembunyikan kartu utama
                } else {
                    // KASUS 2: Tidak sedang mencari
                    productContainer.removeClass('d-none'); // Tampilkan grid produk
                    mainProductCard.show();       // Tampilkan kartu utama
                    resultsContainer.hide();      // Sembunyikan container search
                }
                
                // Pastikan container cart disembunyikan
                cartContainer.addClass('d-none');
            });

            btnShowCart.on('click', function() {
                $(this).addClass('active');
                btnShowProducts.removeClass('active');
                
                // Tampilkan panel keranjang
                cartContainer.removeClass('d-none');
                mainProductCard.show(); // Pastikan kartu utama terlihat (karena cart ada di dalamnya)
                
                // Sembunyikan yang lain
                productContainer.addClass('d-none');
                resultsContainer.hide(); // PENTING: Sembunyikan hasil search jika pindah ke cart
                
                // Jangan kosongkan input search, agar user bisa kembali ke hasil pencarian nanti
                searchInput.val(''); 
                
                // Muat data keranjang
                loadCartData();
            });

            // =======================================================
            // ==     FUNGSI SEARCH PRODUCT (LOGIKA TAMBAHAN)       ==
            // =======================================================

            if (searchInput.length > 0) {
                searchInput.on('input', function() {
                    // Jika user mengetik, otomatis pindahkan highlight tombol ke "Product"
                    if (!btnShowProducts.hasClass('active')) {
                        btnShowProducts.addClass('active');
                        btnShowCart.removeClass('active');
                        cartContainer.addClass('d-none');
                    }

                    const searchTerm = $(this).val().trim();

                    if (searchTerm === '') {
                        // Jika search kosong, kembali ke tampilan produk utama
                        resultsContainer.hide().html(''); // Sembunyikan hasil search
                        mainProductCard.show(); // Tampilkan kartu produk/keranjang
                        return;
                    }

                    // Jika ada text, Sembunyikan kartu produk/keranjang
                    mainProductCard.hide();
                    // Tampilkan kontainer hasil search (yang ada di luar kartu utama)
                    resultsContainer.show(); 

                    $.ajax({
                        url: "{{ route('product.search') }}",
                        type: 'GET',
                        data: { query: searchTerm },
                        dataType: 'json',
                        success: function(data) {
                            resultsContainer.html(''); // Kosongkan hasil sebelumnya
                            const resultsCard = $('<div class="card"></div>');
                            let cardContent = `
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Hasil Pencarian untuk "${searchTerm}"</h4>
                                    <div class="row justify-content-center">
                            `;

                            if (data.length > 0) {
                                $.each(data, function(index, product) {
                                    const totalStock = product.stock.reduce((sum, batch) => sum + batch.remaining_stock, 0);
                                    if (totalStock == 0) return true; // 'continue'

                                    const sellPrice = product.sell_price ? product.sell_price : 0;
                                    const formattedPrice = new Intl.NumberFormat('id-ID', {
                                        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                                    }).format(sellPrice);
                                    const priceDisplay = product.sell_price ? formattedPrice : 'Data tidak tersedia';

                                    cardContent += `
                                        <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                                            <div class="card h-100" style="border: 1px solid black"> 
                                                <div class="card-body d-flex flex-column">
                                                    <h5 class="card-title">${product.name}</h5>
                                                    <h6 class="card-subtitle mb-2">
                                                        Harga : <strong>${priceDisplay}</strong>
                                                    </h6>
                                                    <p class="card-subtitle">Stok : <strong>${totalStock}</strong></p>
                                                    <div class="mt-auto"> 
                                                        <div class="btn-wrapper">
                                                            <button type="button" class="btn btn-primary align-items-center add-to-cart-btn" data-product-id="${product.id}">
                                                                <i class="mdi mdi-cart"></i> Tambah Ke Keranjang
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                            } else {
                                cardContent += `<div class="col-12"><p class="text-center">Produk tidak ditemukan.</p></div>`;
                            }

                            cardContent += `</div></div>`; 
                            resultsCard.html(cardContent);
                            resultsContainer.append(resultsCard);
                        },
                        error: function(xhr) {
                            console.error('Error fetching search results:', xhr);
                            resultsContainer.html('<div class="alert alert-danger">Gagal memuat hasil pencarian.</div>');
                        }
                    });
                });
            }

            // =======================================================
            // ==     LOGIKA AJAX KERANJANG (LOAD, ADD, REMOVE)     ==
            // =======================================================

            function loadCartData() {
                cartContainer.html('<p class="text-center">Memuat keranjang...</p>');
                $.ajax({
                    url: '{{ route("cart.items") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // console.log(data.cart);
                        
                        renderCart(data.cart); 
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        cartContainer.html(`<p class="text-center text-danger">Gagal mengambil data: ${errorThrown}</p>`);
                    }
                });
            }

            function renderCart(items) {
                if (!items || Object.keys(items).length === 0) {
                    cartContainer.html('<p class="text-center">Keranjang belanja Anda kosong.</p>');
                    return;
                }

                let total = 0;
                let tableHTML = `
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                for (const id in items) {
                    const item = items[id];
                    const subtotal = item.subtotal_sell;
                    // console.log(items.subtotal_sell);
                    
                    total += subtotal;

                    tableHTML += `
                        <tr id="row-${id}">
                            <td>${item.name}</td>
                            <td>Rp ${ new Intl.NumberFormat('id-ID').format(item.sell_price) }</td>
                            
                            <td style="min-width: 150px;">
                                <div class="d-flex align-items-stretch" role="group" aria-label="Quantity">
                                    {{-- Tombol minus --}}
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-decrease-qty rounded-0 rounded-start" data-product-id="${id}">
                                        <i class="mdi mdi-minus"></i>
                                    </button>
                                    
                                    {{-- Input kuantitas --}}
                                    <input 
                                        type="number" 
                                        class="form-control form-control-sm text-center border-start-0 border-end-0 qty-input" 
                                        id="qty-${id}" 
                                        data-product-id="${id}" 
                                        value="${item.quantity}" 
                                        min="0"
                                        style="width: 60px; height: auto;" {{-- Hapus 'mx-2' dan tambahkan 'height: auto;' --}}
                                    >
                                    
                                    {{-- Tombol plus --}}
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-increase-qty rounded-0 rounded-end" data-product-id="${id}">
                                        <i class="mdi mdi-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td id="subtotal-${id}">Rp ${ new Intl.NumberFormat('id-ID').format(subtotal) }</td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-from-cart-btn" data-product-id="${id}">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                }

                tableHTML += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th colspan="2" id="cart-total">Rp ${ new Intl.NumberFormat('id-ID').format(total) }</th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="text-end p-3">
                        <button class="btn btn-success" id="checkout-btn-trigger">Checkout</button>
                    </div>
                `;
                cartContainer.html(tableHTML);
            }

            // =======================================================
            // ==        LOGIKA TOMBOL TAMBAH KE KERANJANG          ==
            // =======================================================
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault(); 
                let productId = $(this).data('product-id');
                let button = $(this);
                
                $.ajax({
                    url: '{{ route("cart.add") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    beforeSend: function() {
                        button.prop('disabled', true).html('Menambahkan...');
                    },
                    success: function(response) {
                        console.log(response.product);
                        console.log(response.cart);
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.success,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan.';
                        Swal.fire('Gagal', errorMsg, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<i class="mdi mdi-cart"></i> Tambah Ke Keranjang');
                    }
                });
            });
            
            // =======================================================
            // ==  LOGIKA TOMBOL HAPUS PRODUCT DI KERANJANG (CART)  ==
            // =======================================================

            $('#cart-container').on('click', '.remove-from-cart-btn', function() {
                let productId = $(this).data('product-id');
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Produk ini akan dihapus dari keranjang.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("cart.remove") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                product_id: productId
                            },
                            success: function(response) {
                                Swal.fire('Dihapus!', response.success, 'success');
                                loadCartData(); // Muat ulang data keranjang
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', 'Gagal menghapus produk.', 'error');
                            }
                        });
                    }
                });
            });

            // =======================================================
            // ==     LOGIKA TOMBOL TAMBAH KUANTITAS (+) - DIPERBARUI ==
            // =======================================================
            $('#cart-container').on('click', '.btn-increase-qty', function() {
                let productId = $(this).data('product-id');
                let button = $(this);

                $.ajax({
                    url: '{{ route("cart.increaseQtyCart") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', product_id: productId },
                    beforeSend: function() {
                        button.prop('disabled', true);
                        button.siblings('button, input').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log(response.cart);
                            
                            // 1. Update Kuantitas (Ubah .text ke .val)
                            $('#qty-' + productId).val(response.new_quantity);
                            
                            // 2. Update Subtotal
                            let formattedSubtotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(response.total_product_price);
                            $('#subtotal-' + productId).text(formattedSubtotal);
                            
                            // 3. Update Total Keseluruhan
                            let formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(response.total_transaction_price);
                            $('#cart-total').text(formattedTotal);
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Stok tidak mencukupi atau terjadi error.';
                        Swal.fire('Gagal', errorMsg, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        button.siblings('button, input').prop('disabled', false);
                    }
                });
            });

            // =======================================================
            // ==        LOGIKA TOMBOL KURANG KUANTITAS (-)         ==
            // =======================================================
            $('#cart-container').on('click', '.btn-decrease-qty', function() {
                let productId = $(this).data('product-id');
                let button = $(this);

                $.ajax({
                    url: '{{ route("cart.decreaseQtyCart") }}', // Sesuaikan dengan route Anda
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    beforeSend: function() {
                        button.prop('disabled', true);
                        button.siblings('.btn-increase-qty').prop('disabled', true);
                    },
                    success: function(response) {
                        console.log(response.cart);
                        if (response.success) {
                            // console.log(response.message);
                            // console.log(response.cart);
                            
                            if (response.unset_item) {
                                // Jika item dihapus (kuantitas jadi 0), muat ulang keranjang
                                Swal.fire('Berhasil', response.message, 'success');
                                loadCartData(); // Cukup muat ulang cart
                            } else {
                                // 1. Update Kuantitas
                                $('#qty-' + productId).val(response.new_quantity);
                                
                                // 2. Update Subtotal
                                let formattedSubtotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(response.total_product_price);
                                $('#subtotal-' + productId).text(formattedSubtotal);
                                
                                // 3. Update Total Keseluruhan
                                let formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(response.total_transaction_price);
                                $('#cart-total').text(formattedTotal);
                            }

                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', 'Gagal mengurangi kuantitas.', 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        button.siblings('.btn-increase-qty').prop('disabled', false);
                    }
                });
            });

            // =======================================================
            // ==     LOGIKA BARU: INPUT KUANTITAS MANUAL           ==
            // =======================================================
            // 'change' akan aktif saat user selesai mengetik & klik di luar input
            $('#cart-container').on('change', '.qty-input', function() {
                let input = $(this);
                let productId = input.data('product-id');
                let newQuantity = parseInt(input.val());

                if (isNaN(newQuantity)) { // Jika user mengetik "abc"
                    newQuantity = 1;
                }

                $.ajax({
                    url: '{{ route("cart.updateQtyCart") }}', // Route baru Anda
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        quantity: newQuantity
                    },
                    beforeSend: function() {
                        input.prop('disabled', true);
                        input.siblings('button').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            
                            if (response.unset_item) {
                                Swal.fire('Dihapus', response.message, 'success');
                                loadCartData(); 
                                return;
                            }

                            // Tampilkan pesan jika stok dibatasi
                            if (response.stock_limited) {
                                Swal.fire({
                                    title: 'Info Stok',
                                    text: response.message,
                                    icon: 'warning',
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                            }

                            // Update UI
                            // 1. Set input value (penting jika server membatasi stok)
                            input.val(response.new_quantity); 
                            
                            // 2. Update Subtotal
                            let formattedSubtotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(response.total_product_price);
                            $('#subtotal-' + productId).text(formattedSubtotal);
                            
                            // 3. Update Total Keseluruhan
                            let formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(response.total_transaction_price);
                            $('#cart-total').text(formattedTotal);

                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                            loadCartData(); 
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan saat update.', 'error');
                        loadCartData(); // Muat ulang utk reset jika terjadi error
                    },
                    complete: function() {
                        input.prop('disabled', false);
                        input.siblings('button').prop('disabled', false);
                    }
                });
            });

            // =======================================================
            // ==           LOGIKA MODAL CHECKOUT                 ==
            // =======================================================
            
            $('#cart-container').on('click', '#checkout-btn-trigger', function() {
                let totalText = $('#cart-container').find('tfoot th:last-child').text();
                let totalNumber = parseFloat(totalText.replace('Rp', '').replace(/\./g, '').trim());

                if (isNaN(totalNumber) || totalNumber <= 0) {
                    Swal.fire('Error', 'Total belanja tidak valid atau keranjang kosong.', 'error');
                    return;
                }

                $('#modal-total-display').val(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(totalNumber));
                $('#modal-total-hidden').val(totalNumber);
                $('#payment-method-select').val('Tunai');
                $('#cash-payment-section').show();
                $('#amount-paid-input').prop('required', true).val('');
                $('#change-display').text('Kembalian: Rp 0').removeClass('text-danger').addClass('text-success');

                $('#checkoutModal').modal('show');
            });

            $('#payment-method-select').on('change', function() {
                let total = parseFloat($('#modal-total-hidden').val());
                if ($(this).val() === 'QRIS') { // Asumsi Anda akan menambah 'QRIS'
                    $('#cash-payment-section').hide();
                    $('#amount-paid-input').val(total).prop('required', false);
                } else { // Tunai
                    $('#cash-payment-section').show();
                    $('#amount-paid-input').prop('required', true).val('');
                    $('#change-display').text('Kembalian: Rp 0');
                }
            });

            // =======================================================
            // ==      LOGIKA INPUT UANG PEMBAYARAN TRANSAKSI       ==
            // =======================================================

            $('#amount-paid-display').on('keyup', function(e) {
    
                // 1. Ambil nilai input
                let value = $(this).val();
                
                // 2. Bersihkan nilai dari "Rp" atau titik (misal: "Rp 10.000" -> "10000")
                //    Gunakan .replace(/[^0-9]/g, '') untuk menghapus semua yg bukan angka
                let rawValue = value.replace(/[^0-9]/g, '');

                // 3. Simpan angka bersih ke input tersembunyi (hidden)
                $('#amount-paid-hidden').val(rawValue);

                // 4. Format ulang angka bersih (misal: "10000" -> "10.000")
                //    Gunakan regex untuk menambahkan titik setiap 3 digit
                let formattedValue = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // 5. Tampilkan nilai yang sudah diformat di input yang terlihat
                $(this).val(formattedValue);

                // --- LOGIKA HITUNG KEMBALIAN (DIMODIFIKASI) ---
                
                // Ambil total dari input tersembunyi
                let total = parseFloat($('#modal-total-hidden').val());
                
                // Ambil jumlah bayar dari 'rawValue', bukan dari '.val()'
                let amountPaid = parseFloat(rawValue || 0); // (Gunakan || 0 jika input kosong)
                
                if (isNaN(amountPaid)) amountPaid = 0;
                
                let change = amountPaid - total;

                // Tampilkan kembalian (sudah diformat)
                let formattedChange = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0 
                }).format(change);

                if (change < 0) {
                    $('#change-display').text('Uang Kurang: ' + formattedChange);
                    $('#change-display').removeClass('text-success').addClass('text-danger');
                } else {
                    $('#change-display').text('Kembalian: ' + formattedChange);
                    $('#change-display').removeClass('text-danger').addClass('text-success');
                }
            });

            // =======================================================
            // ==           LOGIKA MODAL EDIT PRODUK              ==
            // =======================================================
            // (Kode ini dari file Anda, dipindahkan ke sini)

            $(document).on('click', '.edit-product-btn', function () {
                let productId = $(this).data('productid');
                let productName = $(this).data('productname');
                let categoryId = $(this).data('categoryid');
                let categoryName = $(this).data('categoryname');

                $('#editProductForm #product_id').val(productId);
                $('#editProductForm #product_name').val(productName);
                $('#editProductForm #selected_category_id').val(categoryId);
                $('#editProductForm #category_dropdown_button').text(categoryName);

                originalProductName = productName;
                originalCategoryName = categoryName;
                
                $('#editCategoryForm #category_name').removeClass('is-invalid');
                $('#editCategoryForm #name_error').text('');

                $saveButton.prop('disabled', true);
                $('#editProductModal').modal('show');
            });

            $(document).on('click', '.modal-close-btn', function () {
                $('#editProductModal').modal('hide');
            });

            $('#editProductForm #product_name').on('input', function () {
                let currentValue = $(this).val();
                $saveButton.prop('disabled', currentValue === originalProductName);
            });

            $(document).on('click', '#editProductModal #category_options .dropdown-item', function(e) {
                e.preventDefault();
                const selectedCategoryId = $(this).data('id');
                const selectedCategoryName = $(this).data('name');

                $('#editProductModal #category_dropdown_button').text(selectedCategoryName);
                $('#editProductModal #selected_category_id').val(selectedCategoryId);

                if (selectedCategoryName != originalCategoryName) {
                    $('#btnSaveProductEdit').prop('disabled', false);
                } else {
                    $('#btnSaveProductEdit').prop('disabled', true);
                }
            });

            $('#editProductForm').on('hidden.bs.modal', function () {
                $saveButton.prop('disabled', true);
                originalCategoryName = '';
            });

        });
    </script>
@endpush


