<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PurchasingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // return view('stock_in');
        $stockInItems = Session::get('stock_in_cart', []);
        return view('stock_in', compact('stockInItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $stockInItems = Session::get('stock_in_cart', []);
        return view('stock_in', compact('stockInItems'));
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
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
         if ($request->ajax()) {
            $query = $request->get('query');
            if ($query != '') {
                $products = Product::with('category', 'stock')
                                ->where('name', 'like', '%'.$query.'%')
                                ->limit(10)
                                ->get();
            } else {
                $products = [];
            }
            return response()->json($products);
        }
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        $product = Product::with('category')->find($request->product_id);

        $cartItem = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'category_name' => $product->category->name,
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price,
        ];

        // Menggunakan session helper untuk menambahkan item ke array
        Session::push('stock_in_cart', $cartItem);

        return back()->with('success', 'Produk berhasil ditambahkan ke daftar.');
    }

    public function process(Request $request)
    {

         // Validasi data yang masuk dalam bentuk array
        $validator = Validator::make($request->all(), [
            'product_id.*' => 'required|exists:products,id',
            'quantity.*' => 'required|integer|min:1',
            'purchase_price.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Loop melalui setiap baris yang dikirim dari form
            foreach ($request->product_id as $key => $productId) {
                $product = Product::find($productId);
                // 1. Tambahkan data ke tabel 'stocks' melalui Model 'Stock'
                Stock::create([
                    'product_id' => $productId,
                    'initial_stock' => $request->quantity[$key],
                    'remaining_stock' => $request->quantity[$key],
                    'buy_price' => $request->purchase_price[$key],
                    'sell_price' => $product->stock->first()->sell_price
                ]);
            }

            DB::commit();

            return back()->with('success', 'Semua stok berhasil ditambahkan ke database.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses stok: ' . $e->getMessage());
        }

    }
}

