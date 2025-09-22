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
        $cart = session()->get('cart');
        dd($cart);
        return view('cart', [

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

    public function addToCart(Request $request, $id){
        // dd($id);
        $product = Product::findOrFail($id);
        $batch = ProductStockBatches::where('product_id', $id)->first();
        $batches = ProductStockBatches::where('product_id', $id)->get();
        $total_stock = 0;
        
        foreach($batches as $item){
            $total_stock += $item->initial_stock;
        }

        // dd($total_stock);

        $cart = session()->get('cart', []);

        if(isset($cart[$id])){
            $cart[$id]['quantity']++;
        }
        else{
            $cart[$id] = [
                'name' => $product->name,
                'stock' => $total_stock,
                'sell_price' => $batch->sell_price,
                'quantity' => 1
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('add_to_cart_success', "Product Berhasil Ditambahkan Ke Keranjang!");
    }
}
