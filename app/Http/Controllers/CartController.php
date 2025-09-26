<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductStockBatches;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $carts = session()->get('cart');
        // dd($carts);
        return view('cart', [
            'carts' => $carts,
        ]);
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
    // public function destroy(string $id)
    // {
    //     //
    //     session()->forget('cart');
    //     return redirect()->back();
    // }

    public function destroy()
    {
        //
        session()->forget('cart');
        return redirect()->back();
    }

    public function addToCart(Request $request){

        $id = $request->input('product_id'); 
        // dd($id);
        $product = Product::with('category', 'stockBatches')->findOrFail($id);

        // dd($total_stock);
        // dd($product->stockBatches->pluck('sell_price'));
        // dd($product->stockBatches->pluck('sell_price')->sum());
        // dd($product->category->name);

        $cart = session()->get('cart', []);

        if(isset($cart[$id])){
            $cart[$id]['quantity']++;
        }
        else{
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name,
                'stock' => $product->stockBatches->pluck('remaining_stock')->sum(),
                'sell_price' => $product->stockBatches->first()->sell_price,
                'quantity' => 1
            ];
        }

        $qty = $cart[$id]['quantity'];

        session()->put('cart', $cart);
        // return redirect()->back()->with('add_to_cart_success', "Product Berhasil Ditambahkan Ke Keranjang!");
        return response()->json([
            'success' => true,
            'message' => "Product Berhasil Ditambahkan Ke Keranjang!",
            'cart_count' => count($cart), // Kirim jumlah item di keranjang
            'qty' => $qty,
        ]);
    }

    public function increaseQtyCart(Request $request){

        $id = $request->input('product_id');
        $cart = session()->get('cart', []);
        $itemFound = false;

        foreach ($cart as &$item) {
            if ($item['id'] == $id) {
                $itemFound = true;
                
                $product = Product::with('stockBatches')->find($id);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Produk tidak ditemukan.",
                    ], 404);
                }

                $totalStock = $product->stockBatches->sum('remaining_stock');

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
                    'total_transaction_price' => $totalPrice
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

        $id = $request->input('product_id');
        $cart = session()->get('cart', []);
        $newQuantity = 0;
        $itemRemoved = false;
        $totalPrice = 0;
        
        foreach ($cart as &$item) {
            if ($item['id'] == $id) {
                $item['quantity']--;
                $newQuantity = $item['quantity']; // Simpan kuantitas baru
                if ($item['quantity'] <= 0) {
                    unset($cart[$id]);
                    $itemRemoved = true;
                }
                session()->put('cart', $cart);
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
            // 'total_product_price' => $price,
            'total_transaction_price' => $totalPrice
        ]);
    }
}
