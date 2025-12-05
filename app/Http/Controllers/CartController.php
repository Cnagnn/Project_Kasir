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
        ], [
            'product_id.required' => 'Produk harus dipilih.',
            'product_id.exists' => 'Produk tidak valid.',
        ]);

        try {
            // 2. Ambil Produk beserta SEMUA stok yang tersedia (FIFO)
            $product = Product::with(['stock' => function($query) {
                                    $query->where('remaining_stock', '>', 0)
                                        ->orderBy('created_at', 'asc'); // Kunci FIFO: Batch terlama dulu
                                }])
                                ->withSum('stock as total_stock', 'remaining_stock')
                                ->findOrFail($request->product_id);

            // 3. Siapkan Variabel
            $cart = session()->get('cart', []);
            $productId = $product->id;
            
            // Tentukan jumlah target (Qty saat ini + 1 yang baru ditambah)
            $currentQtyInCart = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
            $targetQty = $currentQtyInCart + 1;

            // 4. Cek Ketersediaan Stok Total
            $totalAvailableStock = $product->total_stock ?? 0;
            
            if ($targetQty > $totalAvailableStock) {
                return response()->json(['error' => 'Stok produk tidak mencukupi! Total sisa: ' . $totalAvailableStock], 422);
            }

            // ==================================================================
            // 5. LOGIKA HITUNG ULANG FIFO (CORE ALGORITHM)
            // Kita hitung ulang total harga beli dan jual untuk $targetQty
            // dengan mengambil "jatah" dari masing-masing batch secara berurutan.
            // ==================================================================
            
            $qtyToFill = $targetQty; // Sisa qty yang harus dicarikan harganya (misal: 6)
            $accumulatedBuyPrice = 0;  
            $accumulatedSellPrice = 0; 
            
            // Harga satuan terakhir (untuk referensi tampilan jika perlu)
            $lastBatchSellPrice = 0;
            $lastBatchBuyPrice = 0;

            foreach ($product->stock as $batch) {
                // Jika kebutuhan sudah terpenuhi, hentikan loop
                if ($qtyToFill <= 0) break;

                // Ambil stok dari batch ini. 
                // Ambil yang lebih kecil: Sisa yang dibutuhkan ATAU Sisa stok di batch ini
                $take = min($qtyToFill, $batch->remaining_stock);
                
                // Akumulasi Harga
                $accumulatedBuyPrice += ($take * $batch->buy_price);
                $accumulatedSellPrice += ($take * $batch->sell_price);
                
                // Simpan harga batch ini sebagai referensi harga terakhir
                $lastBatchBuyPrice = $batch->buy_price;
                $lastBatchSellPrice = $batch->sell_price;

                // Kurangi sisa kebutuhan
                $qtyToFill -= $take;
            }

            // ==================================================================
            // 6. UPDATE SESSION
            // ==================================================================

            $profit = $accumulatedSellPrice - $accumulatedBuyPrice;

            $cart[$productId] = [
                "id"            => $product->id,
                "name"          => $product->name,
                "quantity"      => $targetQty,
                
                // Harga Satuan (Unit Price)
                // Kita bisa menampilkan harga rata-rata, atau harga dari batch terakhir.
                // Di sini saya set harga batch terakhir agar sesuai logika penambahan
                "sell_price"    => $lastBatchSellPrice, 
                "buy_price"     => $lastBatchBuyPrice,

                // SUBTOTAL yang AKURAT (Hasil akumulasi FIFO)
                "subtotal_sell" => $accumulatedSellPrice, // Total yang harus dibayar konsumen
                "subtotal_buy"  => $accumulatedBuyPrice,  // Total modal kita
                
                // PROFIT yang AKURAT
                "profit"        => $profit
            ];

            // 7. Simpan kembali ke session
            session()->put('cart', $cart);

            return response()->json([
                'success' => $product->name . ' berhasil ditambahkan!',
                'cart'    => $cart, // Kirim untuk dicek di console
                'debug'   => [
                    'qty_request' => $targetQty,
                    'total_modal' => $accumulatedBuyPrice,
                    'total_omzet' => $accumulatedSellPrice
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function increaseQtyCart(Request $request){

        // 1. Ambil ID Produk & Session
        $productId = $request->product_id;
        $cart = session()->get('cart', []);

        // Cek apakah item ada di keranjang
        if (!isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => "Item tidak ditemukan di keranjang."], 404);
        }

        // 2. Ambil Data Produk & Stok Real-time (FIFO)
        // Kita ambil semua stok yang > 0, urutkan dari yang terlama
        $product = Product::with(['stock' => function($query) {
                                $query->where('remaining_stock', '>', 0)
                                    ->orderBy('created_at', 'asc');
                            }])
                            ->withSum('stock as total_stock', 'remaining_stock')
                            ->find($productId);

        if (!$product) {
            return response()->json(['success' => false, 'message' => "Data produk tidak ditemukan di database."], 404);
        }

        // 3. Tentukan Target Kuantitas Baru
        $currentQty = $cart[$productId]['quantity'];
        $newQty = $currentQty + 1;

        // 4. Cek Apakah Total Stok Mencukupi
        $totalAvailableStock = $product->total_stock ?? 0;

        if ($newQty > $totalAvailableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stok maksimal tercapai! Sisa stok tersedia: " . $totalAvailableStock,
                'total_stock' => $totalAvailableStock
            ]);
        }

        // ==================================================================
        // 5. LOGIKA HITUNG ULANG (RECALCULATE) FIFO
        // ==================================================================
        
        $qtyToFill = $newQty;          // Misal: 6
        $accumulatedBuyPrice = 0;      // Total Modal
        $accumulatedSellPrice = 0;     // Total Jual
        
        $lastBatchSellPrice = 0;       // Untuk update harga satuan di UI (opsional)

        foreach ($product->stock as $batch) {
            if ($qtyToFill <= 0) break;

            // Ambil "jatah" dari batch ini
            // Min: Sisa yang dibutuhkan vs Sisa stok di batch
            $take = min($qtyToFill, $batch->remaining_stock);
            
            // Akumulasi
            $accumulatedBuyPrice += ($take * $batch->buy_price);
            $accumulatedSellPrice += ($take * $batch->sell_price);
            
            // Update referensi harga terakhir
            $lastBatchSellPrice = $batch->sell_price;

            // Kurangi sisa kebutuhan
            $qtyToFill -= $take;
        }

        // ==================================================================
        // 6. UPDATE SESSION
        // ==================================================================

        $cart[$productId]['quantity'] = $newQty;
        
        // Update Subtotal & Profit dengan hasil hitungan baru
        $cart[$productId]['subtotal_sell'] = $accumulatedSellPrice;
        $cart[$productId]['subtotal_buy']  = $accumulatedBuyPrice;
        $cart[$productId]['profit']        = $accumulatedSellPrice - $accumulatedBuyPrice;
        
        // Update harga satuan (opsional, agar UI menampilkan harga batch terakhir)
        $cart[$productId]['sell_price'] = $lastBatchSellPrice;

        session()->put('cart', $cart);

        // ==================================================================
        // 7. HITUNG TOTAL TRANSAKSI
        // ==================================================================
        
        $totalTransaction = 0;
        foreach ($cart as $item) {
            // Pastikan menggunakan key 'subtotal_sell' yang sudah kita standarisasi
            $totalTransaction += $item['subtotal_sell'];
        }

        return response()->json([
            'success' => true,
            'message' => "Kuantitas berhasil ditambah!",
            'new_quantity' => $newQty,
            // Data untuk update tampilan per baris
            'total_product_price' => $accumulatedSellPrice, 
            // Data untuk update total bawah
            'total_transaction_price' => $totalTransaction,
            'cart' => $cart // Debugging
        ]);
    }

    public function decreaseQtyCart(Request $request){

        $productId = $request->product_id;
        $cart = session()->get('cart', []);

        // 1. Cek apakah item ada di keranjang
        if (!isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => "Item tidak ditemukan."], 404);
        }

        // 2. Hitung Kuantitas Baru
        $currentQty = $cart[$productId]['quantity'];
        $newQty = $currentQty - 1;

        // 3. SKENARIO HAPUS ITEM: Jika kuantitas menjadi 0
        if ($newQty <= 0) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            // Hitung ulang total transaksi keranjang
            $totalTransaction = 0;
            foreach ($cart as $item) {
                $totalTransaction += $item['subtotal_sell'];
            }

            return response()->json([
                'success' => true,
                'unset_item' => true, // Flag untuk memberitahu JS agar menghapus baris
                'message' => "Produk dihapus dari keranjang.",
                'total_transaction_price' => $totalTransaction
            ]);
        }

        // 4. AMBIL DATA TERBARU DARI DB (Untuk Recalculate FIFO)
        // Langkah ini PENTING agar perhitungan subtotal tetap akurat sesuai komposisi batch
        $product = Product::with(['stock' => function($query) {
                                $query->where('remaining_stock', '>', 0)
                                    ->orderBy('created_at', 'asc');
                            }])->find($productId);

        if (!$product) {
            // Edge case: Jika produk dihapus dari DB saat user transaksi
            return response()->json(['success' => false, 'message' => "Data produk hilang dari database."], 404);
        }

        // ==================================================================
        // 5. LOGIKA HITUNG ULANG (RECALCULATE) FIFO
        // ==================================================================
        
        $qtyToFill = $newQty;          
        $accumulatedBuyPrice = 0;      
        $accumulatedSellPrice = 0;     
        
        // Kita perlu tahu harga satuan item terakhir yang tersisa (untuk update UI)
        $lastBatchSellPrice = 0;       

        foreach ($product->stock as $batch) {
            if ($qtyToFill <= 0) break;

            // Ambil stok dari batch ini
            $take = min($qtyToFill, $batch->remaining_stock);
            
            // Akumulasi Harga
            $accumulatedBuyPrice += ($take * $batch->buy_price);
            $accumulatedSellPrice += ($take * $batch->sell_price);
            
            // Update referensi harga terakhir
            $lastBatchSellPrice = $batch->sell_price;

            $qtyToFill -= $take;
        }

        // ==================================================================
        // 6. UPDATE SESSION
        // ==================================================================

        $cart[$productId]['quantity'] = $newQty;
        
        // Simpan data hasil hitung ulang
        $cart[$productId]['subtotal_sell'] = $accumulatedSellPrice;
        $cart[$productId]['subtotal_buy']  = $accumulatedBuyPrice;
        $cart[$productId]['profit']        = $accumulatedSellPrice - $accumulatedBuyPrice;
        
        // Update harga satuan (opsional, harga dari item paling akhir dalam tumpukan)
        $cart[$productId]['sell_price'] = $lastBatchSellPrice;

        session()->put('cart', $cart);

        // ==================================================================
        // 7. HITUNG TOTAL TRANSAKSI
        // ==================================================================
        
        $totalTransaction = 0;
        foreach ($cart as $item) {
            $totalTransaction += $item['subtotal_sell'];
        }

        return response()->json([
            'success' => true,
            'message' => "Kuantitas berhasil dikurangi.",
            'new_quantity' => $newQty,
            // Data subtotal baru untuk update baris tabel
            'total_product_price' => $accumulatedSellPrice, 
            // Data total baru untuk footer tabel
            'total_transaction_price' => $totalTransaction,
            'cart' => $cart 
        ]);
    }

    public function updateQtyCart(Request $request)
    {
        // 1. Ambil Input
        // Pastikan di JS anda mengirim 'product_id' dan 'quantity'
        $productId = $request->product_id; 
        $requestedQty = (int) $request->quantity;
        $cart = session()->get('cart', []);

        // Cek item di session
        if (!isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => "Item tidak ditemukan."], 404);
        }

        // 2. Handle Jika User Input 0 atau Negatif
        if ($requestedQty <= 0) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            // Hitung ulang total
            $totalTransaction = 0;
            foreach ($cart as $item) {
                $totalTransaction += $item['subtotal_sell'];
            }

            return response()->json([
                'success' => true,
                'unset_item' => true,
                'message' => "Produk dihapus dari keranjang.",
                'total_transaction_price' => $totalTransaction
            ]);
        }

        // 3. Ambil Data DB Real-time (FIFO)
        $product = Product::with(['stock' => function($query) {
                                $query->where('remaining_stock', '>', 0)
                                    ->orderBy('created_at', 'asc');
                            }])
                            ->withSum('stock as total_stock', 'remaining_stock')
                            ->find($productId);

        if (!$product) {
            return response()->json(['success' => false, 'message' => "Produk tidak ditemukan di database."], 404);
        }

        // 4. Validasi & Pembatasan Stok (Capping)
        $totalAvailableStock = $product->total_stock ?? 0;
        $finalQty = $requestedQty;
        $stockLimited = false;
        $message = "Kuantitas berhasil diupdate.";

        // Jika user minta 100 tapi stok cuma 50, kita paksa jadi 50
        if ($requestedQty > $totalAvailableStock) {
            $finalQty = $totalAvailableStock;
            $stockLimited = true;
            $message = "Stok tidak mencukupi! Jumlah disesuaikan ke sisa stok: " . $totalAvailableStock;
        }

        // ==================================================================
        // 5. LOGIKA HITUNG ULANG (RECALCULATE) FIFO
        // ==================================================================
        
        $qtyToFill = $finalQty;
        $accumulatedBuyPrice = 0;
        $accumulatedSellPrice = 0;
        $lastBatchSellPrice = 0;

        foreach ($product->stock as $batch) {
            if ($qtyToFill <= 0) break;

            // Ambil stok dari batch ini
            $take = min($qtyToFill, $batch->remaining_stock);
            
            // Akumulasi
            $accumulatedBuyPrice += ($take * $batch->buy_price);
            $accumulatedSellPrice += ($take * $batch->sell_price);
            
            $lastBatchSellPrice = $batch->sell_price;

            $qtyToFill -= $take;
        }

        // ==================================================================
        // 6. UPDATE SESSION
        // ==================================================================

        $cart[$productId]['quantity'] = $finalQty;
        $cart[$productId]['subtotal_sell'] = $accumulatedSellPrice;
        $cart[$productId]['subtotal_buy']  = $accumulatedBuyPrice;
        $cart[$productId]['profit']        = $accumulatedSellPrice - $accumulatedBuyPrice;
        $cart[$productId]['sell_price']    = $lastBatchSellPrice;

        session()->put('cart', $cart);

        // ==================================================================
        // 7. HITUNG TOTAL TRANSAKSI
        // ==================================================================
        
        $totalTransaction = 0;
        foreach ($cart as $item) {
            $totalTransaction += $item['subtotal_sell'];
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'new_quantity' => $finalQty, // Kirim balik qty yang sudah divalidasi
            'total_product_price' => $accumulatedSellPrice,
            'total_transaction_price' => $totalTransaction,
            'stock_limited' => $stockLimited, // Flag untuk trigger alert di JS
            'cart' => $cart
        ]);
    }

    public function removeFromCart(Request $request)
    {
        // 1. Validasi request, pastikan product_id ada
        $request->validate([
            'product_id' => 'required|integer'
        ], [
            'product_id.required' => 'ID produk harus ada.',
            'product_id.integer' => 'ID produk harus berupa angka.',
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
