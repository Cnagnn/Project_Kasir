<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // mengambil data dari table categories
    	// $products = Product::with('category', 'stockBatches')->get();
        // $categories = Categories::with('product')->get();
        $categories = Categories::all();
        // dd($products);
    	// mengirim data categories ke view 
    	return view('category',[
            // 'products' => $products,
            'categories' => $categories,
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
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $page = $request->input('page');
        // dd($page);

        $category = Categories::where('name', $request->name)->first();

        if(is_null($category)){
            Categories::create([
                'name' => $request->name,
            ]);
        }
        
        // if ($page === "product_page") {
        //     return redirect()->route('product.index')->with(['category_add_success' => 'Data Kategori Berhasil Disimpan!']);
        // }
        // else{
        //      return redirect()->route('category.index')->with(['category_add_success' => 'Data Kategori Berhasil Disimpan!']);
        // }
        return Redirect::back()->with(['category_add_success' => 'Data Kategori Berhasil Disimpan!']);
    
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
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
        // Ambil keyword pencarian dari query string (?query=...)
        $query = $request->input('query');

        // Lakukan pencarian di database
        $categories = Categories::where('name', 'LIKE', "%{$query}%")
            // ->with('category', 'stockBatches') // Eager load category untuk efisiensi
            ->take(10) // Batasi hasil agar tidak terlalu banyak
            ->get();

            // dd($products);
        // Kembalikan hasil dalam format JSON
        return response()->json($categories);
    }

    public function productsByCategory(string $id)
    {
        
        $products = Product::where('category_id', '=', $id)
                            ->with('stockBatches')
                            ->get(); 
        // dd($products);
        $category = Categories::where('id', '=', $id)->first();
        // dd($categoryName->name);
        $categories = Categories::all();

        return view('category_detail',[
            'categoryName' => $category->name,
            'categoryId' => $category->id,
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function categoryProductDetail(string $id)
    {
        // dd($id);
        // Cari data produk berdasarkan id
        $product = Product::findOrFail($id); 

        // Menggabung tabel product dengan tabel stockBatches untuk merelasi semua batch yang dimiliki oleh produk
        $product->load(['stockBatches' => function ($query) {
            $query->orderBy('created_at', 'desc'); // Urutkan berdasarkan tanggal dibuat (terbaru dulu)
        }]);

        // Ambil semua kategori untuk dropdown
        $categories = Categories::all();

        $product_category = $product->category_id;
        // dd($product->category_id);


        // Kirim data ke view baru
        return view('category_product_batch', [
            'product' => $product,
            'categories' => $categories,
            'product_category' => $product_category,
        ]);
    }

    
}
