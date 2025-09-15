<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockBatches;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // mengambil data dari table product
    	$products = Product::with('category', 'ProductStockBatches')->latest()->get();
        $categories = Categories::all();
    	// mengirim data product ke view 
    	return view('products',[
            'products' => $products,
            'categories' => $categories,
        ]);

        // foreach (Products::all() as $product) {
        //     $product_name = $product->name;
        //     $product_category_id = $product->category_id;
        //     $product_category = $product->categories()->name;
        //     echo "<p>Nama Produk = $product_name</p>";

        //     echo "<p>Kategori Produk = $product_category</p>";
        // }
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
        // dd($request->name);
        //validate form
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'stock' => 'required',
            'buy_price' => 'required',
            'sell_price' => 'required',
        ]);

        $product = Product::where('name', $request->name)->first();
        // dd($product);
        if(is_null($product)){
            //create product
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'image' => 'image',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $product->ProductStockBatches()->create([
                'initial_stock' => $request->stock,
                'remaining_stock' => $request->stock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        else{
            ProductStockBatches::create([
                'product_id' => $product->id,
                'initial_stock' => $request->stock,
                'remaining_stock' => $request->stock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        //redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
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
        // Validate form
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'stock' => 'required',
            'buy_price' => 'required',
            'sell_price' => 'required',
        ]);

        // Find the product
        $product = Product::findOrFail($id);
        
        // Update product basic info
        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'updated_at' => now(),
        ]);

        // Get the latest stock batch for this product
        $latestBatch = $product->ProductStockBatches()->latest()->first();
        
        if ($latestBatch) {
            // Update the latest batch
            $latestBatch->update([
                'initial_stock' => $request->stock,
                'remaining_stock' => $request->stock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'updated_at' => now(),
            ]);
        } else {
            // Create new batch if none exists
            $product->ProductStockBatches()->create([
                'initial_stock' => $request->stock,
                'remaining_stock' => $request->stock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect to index with success message
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diupdate!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
