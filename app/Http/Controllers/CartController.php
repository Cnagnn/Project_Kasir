<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //     
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
        // session()->forget('cart');
        // return redirect()->back();
    }

    public function getCartItems(Request $request)
    {
        // Ambil data keranjang dari session. 
        // Jika 'cart' tidak ada, kembalikan array kosong.
        $cartItems = session()->get('cart', []);
        
        // Cek jika request datang dari AJAX
        if ($request->ajax()) {
            return response()->json([
                'cart' => $cartItems,
            ]);
        }

        // Redirect atau abort jika diakses langsung via browser
        return abort(404);
    }

    public function addToCart(Request $request)
    {
        // 1. Validasi request
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        try {
            // 2. Cari produk DAN hitung total stoknya dengan efisien
            // Kita menggunakan withSum untuk menjumlahkan 'remaining_stock' dari semua batch terkait
            $product = Product::withSum('stock as total_stock', 'remaining_stock')
                              ->findOrFail($request->product_id);

            // 3. Ambil keranjang dari session
            $cart = session()->get('cart', []);
            $productId = $product->id;
            
            // 4. LOGIKA PENGECEKAN STOK (PENTING!)
            $totalStock = $product->total_stock ?? 0; // Ambil total stok, jika tidak ada batch = 0
            $quantityInCart = $cart[$productId]['quantity'] ?? 0; // Cek kuantitas di keranjang

            if ($quantityInCart >= $totalStock) {
                // Kirim error JSON yang BISA dibaca oleh JavaScript Anda
                return response()->json(['error' => 'Stok produk tidak mencukupi!'], 422); 
            }

            // 5. Cek apakah produk sudah ada di keranjang
            if(isset($cart[$productId])) {
                // Jika sudah ada, tambahkan quantity-nya
                $cart[$productId]['quantity']++;
            } else {
                // Jika belum ada, tambahkan sebagai item baru
                // Kita tidak perlu menyimpan buy_price di keranjang
                $cart[$productId] = [
                    "id" => $product->id,
                    "name" => $product->name,
                    "quantity" => 1,
                    "sell_price" => $product->sell_price,
                    // "image" => $product->image // (Jika Anda punya kolom ini)
                ];
            }

            // 6. Simpan kembali keranjang ke dalam session
            session()->put('cart', $cart);

            // 7. Kirim respons berhasil
            return response()->json(['success' => $product->name . ' berhasil ditambahkan ke keranjang!']);

        } catch (\Exception $e) {
            // Tangkap error lain (spt 500)
            return response()->json(['error' => 'Terjadi kesalahan pada server: ' . $e->getMessage()], 500);
        }
    }

    public function increaseQtyCart(Request $request){

        // $response = "oke";

        // return response()->json([
        //     'success' => true,
        //     'message' => $response,
            
        // ]);

        $id = $request->product_id;
        $cart = session()->get('cart', []);
        $itemFound = false;

        // return response()->json([
        //     'success' => true,
        //     'id' => $id, 
        //     'cart' => $cart
        // ]);

        foreach ($cart as &$item) {
            if ($item['id'] == $id) {
                $itemFound = true;
                
                $product = Product::with('stock')->find($id);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Produk tidak ditemukan.",
                    ], 404);
                }

                $totalStock = $product->stock->sum('remaining_stock');

                if ($item['quantity'] >= $totalStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi! Sisa stok: " . $totalStock,
                        'total_stock' => $totalStock
                    ]);
                }

                // 4. Jika stok masih tersedia, tambahkan kuantitas
                $item['quantity']++;
                $newQuantity = $item['quantity'];

                $price = $item['quantity'] * $item['sell_price'];

                // Simpan kembali cart ke session
                session()->put('cart', $cart);

                $updatedCart = session()->get('cart', []);
                $totalPrice = 0;

                foreach ($cart as &$item){
                    $totalPrice = $totalPrice + ($item['quantity'] * $item['sell_price']);
                }

                // Kirim response 'sukses'
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil Menambahkan Kuantitas!",
                    'new_quantity' => $newQuantity,
                    'total_product_price' => $price,    
                    'total_transaction_price' => $totalPrice,
                    'product' => $cart
                ]);
            }
        }
        
        // Jika item tidak ditemukan dalam keranjang sama sekali
        if (!$itemFound) {
            return response()->json([
                'success' => false,
                'message' => "Item tidak ditemukan di keranjang.",
            ], 404);
        }
    }

    public function decreaseQtyCart(Request $request){

        // $response = "oke";

        // return response()->json([
        //     'success' => true,
        //     'message' => $response,
            
        // ]);


        $id = $request->product_id;
        $cart = session()->get('cart', []);
        $newQuantity = 0;
        $itemRemoved = false;
        $totalPrice = 0;

        // return response()->json([
        //     'success' => true,
        //     'cart' => $cart,
            
        // ]);
        
        foreach ($cart as &$item) {
            if ($item['id'] == $id) {
                $item['quantity']--;
                $newQuantity = $item['quantity']; // Simpan kuantitas baru
                if ($item['quantity'] <= 0) {
                    unset($cart[$id]);
                    $itemRemoved = true;
                }
                session()->put('cart', $cart);
                $price = $item['quantity'] * $item['sell_price'];
                $updatedCart = session()->get('cart', []);
                
                foreach ($cart as &$item){
                    $totalPrice = $totalPrice + ($item['quantity'] * $item['sell_price']);
                }
            }
            
        }
        
         // Jika item dihapus, kirim respons khusus untuk penghapusan
        if ($itemRemoved) {
            return response()->json([
                'success' => true,
                'message' => "Produk berhasil dihapus dari keranjang!",
                'unset_item' => true,
            ]);
        }
        // Jika hanya mengurangi kuantitas, kirim respons update
        return response()->json([
            'success' => true,
            'message' => "Berhasil mengurangi kuantitas!",
            'new_quantity' => $newQuantity,
            'total_product_price' => $price,
            'total_transaction_price' => $totalPrice
        ]);
    }

    public function updateQtyCart(Request $request)
    {
        $id = $request->input('product_id'); // Ini adalah KUNCI array
        $newQuantity = (int) $request->input('quantity'); // Kuantitas baru dari input
        $cart = session()->get('cart', []);

        $itemRemoved = false;
        $price = 0;
        $totalPrice = 0;
        $message = "Kuantitas berhasil diupdate."; // Pesan default
        $stockLimited = false; // Flag untuk menandai jika stok dibatasi

        // Cek apakah item ada di keranjang
        if (isset($cart[$id]) && is_array($cart[$id])) {
            
            // Jika kuantitas 0 atau negatif, hapus item
            if ($newQuantity <= 0) {
                unset($cart[$id]);
                $itemRemoved = true;
                $message = "Produk berhasil dihapus dari keranjang.";
            } else {
                // Jika kuantitas > 0, cek stok
                $real_product_id = $cart[$id]['id'];
                $product = Product::with('stock')->find($real_product_id);

                if (!$product) {
                    return response()->json(['success' => false, 'message' => "Data produk tidak ditemukan."], 404);
                }

                $totalStock = $product->stock->sum('remaining_stock');

                // Jika kuantitas yang diminta melebihi stok
                if ($newQuantity > $totalStock) {
                    // Paksa kuantitas menjadi = stok maksimal
                    $newQuantity = $totalStock; 
                    $cart[$id]['quantity'] = $totalStock;
                    // Siapkan pesan peringatan untuk dikirim kembali
                    $message = "Stok tidak mencukupi! Kuantitas diatur ke sisa stok: " . $totalStock;
                    $stockLimited = true;
                } else {
                    // Stok aman, update kuantitas
                    $cart[$id]['quantity'] = $newQuantity;
                }

                // Hitung subtotal baru untuk item ini
                $price = $cart[$id]['quantity'] * $cart[$id]['sell_price'];
            }

            // Simpan keranjang baru ke session
            session()->put('cart', $cart);

            // --- Hitung Ulang Total Harga Keseluruhan ---
            $updatedCart = session()->get('cart', []);
            foreach ($updatedCart as $cartItem) {
                if (is_array($cartItem) && isset($cartItem['quantity'])) {
                    $totalPrice += ($cartItem['quantity'] * $cartItem['sell_price']);
                }
            }
            // --- Akhir Perhitungan Ulang ---

            // Kirim response jika item dihapus
            if ($itemRemoved) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'unset_item' => true,
                    'total_transaction_price' => $totalPrice
                ]);
            }

            // Kirim response normal (update)
            return response()->json([
                'success' => true,
                'message' => $message,
                'new_quantity' => $newQuantity, // Kirim kuantitas yg *sebenarnya* di-set
                'total_product_price' => $price,
                'total_transaction_price' => $totalPrice,
                'stock_limited' => $stockLimited // Kirim flag pembatasan stok
            ]);

        } else {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan di keranjang.'], 404);
        }
    }

    public function removeFromCart(Request $request)
    {
        // 1. Validasi request, pastikan product_id ada
        $request->validate([
            'product_id' => 'required|integer'
        ]);

        // 2. Ambil keranjang dari session
        $cart = session()->get('cart');
        $productId = $request->product_id;

        // 3. Cek apakah produk dengan ID tersebut ada di keranjang
        if (isset($cart[$productId])) {
            // Jika ada, hapus dari array menggunakan unset()
            unset($cart[$productId]);

            // 4. Simpan kembali array keranjang yang sudah diupdate ke session
            session()->put('cart', $cart);
        }

        // 5. Kirim respons sukses dalam format JSON
        return response()->json(['success' => 'Produk berhasil dihapus dari keranjang!']);
    }
}
