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
    	$products = Product::with('category', 'stockBatches')->get();
        $categories = Categories::all();
        // dd($products);
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

            $product->stockBatches()->create([
                'initial_stock' => $request->stock,
                'remaining_stock' => $request->stock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        else{
            stockBatches::create([
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
        return redirect()->route('product.index')->with(['success' => 'Data Berhasil Disimpan!']);
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
        // Cari data produk berdasarkan id
        $product = Product::findOrFail($id); 

        // Menggabung tabel product dengan tabel stockBatches untuk merelasi semua batch yang dimiliki oleh produk
        $product->load(['stockBatches' => function ($query) {
            $query->orderBy('created_at', 'desc'); // Urutkan berdasarkan tanggal dibuat (terbaru dulu)
        }]);

        // Ambil semua kategori untuk dropdown
        $categories = Categories::all();

        // Kirim data ke view baru
        return view('batch', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Validasi semua data yang masuk dari form
        $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);


        // dd($request->name);
        $product = Product::findOrFail($id);
        // dd($product);

        // 2. Lakukan update pada model Product
        $product->update([
            'name' => $request->product_name,
            'category_id' => $request->category_id,
        ]);

        // 3. Kembalikan ke halaman edit dengan pesan sukses
        // Pastikan nama route 'product.edit' ini sesuai dengan nama di file web.php Anda
        return redirect()->route('product.edit', $product->id)
                         ->with('product_edit_success', 'Data produk berhasil diperbarui.');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;
        $product->delete(); // Ini menjalankan Soft Delete

        // Controller mengirim redirect biasa, halaman akan refresh
        // dan menampilkan pesan sukses ini.
        return redirect()->route('product.index')
            ->with('product_delete_success', 'Produk "' . $productName . '" berhasil dihapus!.');
    }
}
