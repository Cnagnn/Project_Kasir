<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        // mengambil data dari table product
    	$products = Product::with('category', 'stock')->paginate(20);
        $categories_all = Category::all();
        $categories_withoutArchived = Category::where('is_archived', '!=', 'yes')->get();
        // dd($categories_withoutArchived);

        // $page = $request->query('from');

    	// mengirim data product ke view 
    	return view('product',[
            'products' => $products,
            'categories' => $categories_all,
            'categories_withoutArchived' => $categories_withoutArchived,
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
        // dd($request);
        //validate form
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            // 'stock' => 'required',
            // 'buy_price' => 'required',
            'sell_price' => 'required',
        ]);

        // Cek apakah ada produk LAIN yang namanya sama (case-insensitive)
        $existingProduct = Product::whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                                    ->where('id', '!=', $request->id) // <-- Kuncinya di sini
                                    ->first();

        // Jika ditemukan produk lain dengan nama itu, kembalikan error
        if ($existingProduct) {
            // Nama sudah dipakai oleh data lain
            return back()->with('failed', 'Nama Product ' . $request->name . ' sudah digunakan.');
        }

        $product = Product::where('name', "{$request->name}")->first();
        // dd($product);
        if(is_null($product)){
            //create product
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'sell_price' => $request->sell_price,
                'image' => 'image',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        else{
            return back()->with('failed', "Data Produk Sudah Ada");
        }

        return Redirect::back()->with(['success' => 'Data Product Berhasil Disimpan!']);
    
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
        $product->load(['stock' => function ($query) {
            $query->orderBy('created_at', 'desc'); // Urutkan berdasarkan tanggal dibuat (terbaru dulu)
        }]);

        // Ambil semua kategori untuk dropdown
        $categories = Category::all();
        // dd($product);
        // Kirim data ke view baru
        return view('product_stock', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // 1. Validasi semua data yang masuk dari form
        $request->validate([
            'product_id' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Cek apakah ada produk LAIN yang namanya sama (case-insensitive)
        $existingProduct = Product::whereRaw('LOWER(name) = ?', [strtolower($request->product_name)])
                                    ->where('id', '!=', $request->product_id) // <-- Kuncinya di sini
                                    ->first();

        // Jika ditemukan produk lain dengan nama itu, kembalikan error
        if ($existingProduct) {
            // Nama sudah dipakai oleh data lain
            return back()->with('failed', 'Nama Product ' . $request->name . ' sudah digunakan.');
        }

        // dd(
        //     $request->product_id,
        //     $request->product_name,
        //     $request->category_id
        // );
        $product_id = $request->product_id;
        $product = Product::findOrFail($product_id);
        // dd($product);

        // 2. Lakukan update pada model Product
        $product->update([
            'name' => $request->product_name,
            'category_id' => $request->category_id,
        ]);

        // 3. Kembalikan ke halaman edit dengan pesan sukses
        return back()->with('success', 'Data produk berhasil diperbarui.')->withInput();
        
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
        return back()->with('success', 'Produk '. $productName .' berhasil dihapus!.');
    }

    public function search(Request $request)
    {
        // Ambil keyword pencarian dari query string (?query=...)
        $query = $request->input('query');

        // Lakukan pencarian di database
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->with('category', 'stock') // Eager load category untuk efisiensi
            ->take(10) // Batasi hasil agar tidak terlalu banyak
            ->get();

            // dd($products);
        // Kembalikan hasil dalam format JSON
        return response()->json($products);
    }

    

}
