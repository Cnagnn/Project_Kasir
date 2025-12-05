<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

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
        $categories = Category::paginate(10);
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
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama kategori harus diisi.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
        ]);

        // 1. Cek apakah ada kategori LAIN yang namanya sama (case-insensitive)
        $existingCategory = Category::whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                                    ->where('id', '!=', $request->id) // <-- Kuncinya di sini
                                    ->first();

        if ($existingCategory) {
            // Nama sudah dipakai oleh data lain
            return back()->with('failed', 'Nama kategori ' . $request->name . ' sudah digunakan.');
        }

        $category = Category::withTrashed()->where('name', $request->name)->first();
        // dd($category);

        if ($category && $category->trashed()) {
            // Jika produk ditemukan dan statusnya terhapus (trashed)
            $category->restore(); // Pulihkan datanya
            
            // Beri pesan bahwa data lama dipulihkan
            $message = 'Category yang sebelumnya dihapus telah berhasil dipulihkan.';

        }
        else if ($category) {
            // Jika produk ditemukan tapi TIDAK terhapus (sudah aktif)
            // Ini berarti ada duplikasi data aktif, kembalikan error.
            return back()->with('failed', 'Category dengan nama ini sudah ada.')->withInput();
        
        } 
        else {
            // Jika produk sama sekali tidak ditemukan, buat baris baru
            Category::create($validated);
            
            // Beri pesan bahwa data baru berhasil dibuat
            $message = 'Category baru berhasil ditambahkan.';
        }

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('category.index')->with('success', $message);
    
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
    public function update(Request $request)
    {
        $id = $request->id;
        $name = $request->name;

        // 1. Cek apakah ada kategori LAIN yang namanya sama (case-insensitive)
        $existingCategory = Category::whereRaw('LOWER(name) = ?', [strtolower($name)])
                                    ->where('id', '!=', $id) // <-- Kuncinya di sini
                                    ->first();

        // 2. Jika ditemukan kategori lain dengan nama itu, kembalikan error
        if ($existingCategory) {
            // Nama sudah dipakai oleh data lain
            return back()->with('failed', 'Nama kategori ' . $name . ' sudah digunakan.');
        }

        // 3. Jika tidak ada duplikat, baru lakukan update
        $category = Category::find($id); // Lebih baik pakai find()

        if ($category) {
            $category->update([
                'name' => $name,
            ]);
            // $category->save(); // TIDAK PERLU, ->update() sudah menyimpan
            return back()->with('success', "Data Kategori Berhasil Diubah");
        }

        // Jika kategori dengan $id tidak ditemukan
        return back()->with('failed', 'Kategori tidak ditemukan.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        // dd($id);
        $category = Category::where('id', $id)->first();
        // dd($category);

        // Hitung jumlah produk yang menggunakan kategori ini
        $productCount = Product::where('category_id', $id)->count();
        // dd($products);

        if($productCount > 0){
            $categoryName = $category->name;
            return redirect()->back()->with('failed', 'Kategori ' . $categoryName . ' tidak dapat dihapus karena masih digunakan oleh ' . $productCount . ' produk.');
        }
        else{
            $category->delete();
            return redirect()->back()->with('success', 'Kategori ' . $category->name . ' berhasil dihapus.');
        }
    }

    public function archive(string $id)
    {
        //
        // dd($id);
        $category = Category::where('id', $id)->first();
        // dd($category);

        if ($category->is_archived === 'no') {

            $category->update([
                'is_archived' => 'yes',
            ]);

            $category->save();

            return back()->with('success', "Data Kategori Berhasil Diarsipkan");
        }
        else {

            $category->update([
                'is_archived' => 'no',
            ]);

            $category->save();

            return back()->with('success', "Data Kategori Berhasil Pulihkan");
        }

        
    }

    public function search(Request $request)
    {
        // Ambil keyword pencarian dari query string (?query=...)
        $query = $request->input('query');

        // Lakukan pencarian di database
        $categories = Category::where('name', 'LIKE', "%{$query}%")
            // ->with('category', 'stockBatches') // Eager load category untuk efisiensi
            ->take(10) // Batasi hasil agar tidak terlalu banyak
            ->get();

            // dd($products);
        // Kembalikan hasil dalam format JSON
        return response()->json($categories);
    }

    public function productsByCategory(string $id)
    {
        
        // Eager loading untuk menghindari N+1 query problem
        $products = Product::where('category_id', '=', $id)
                            ->with(['stock', 'category'])
                            ->get(); 
        
        $category = Category::findOrFail($id);
        // dd($categoryName->name);
        $categories = Category::all();

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
        $product->load(['stock' => function ($query) {
            $query->orderBy('created_at', 'desc'); // Urutkan berdasarkan tanggal dibuat (terbaru dulu)
        }]);

        // Ambil semua kategori untuk dropdown
        $categories = Category::all();

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
