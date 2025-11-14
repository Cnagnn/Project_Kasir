<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SellingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // $stockOutItems = Session::get('stock_out_cart', []);
        // return view('selling', compact('stockOutItems'));
        $products = Product::with('category', 'stock')->paginate(20);
        // $stock_product = Product::with('stock')->sum('remaining_stock');
        // dd($stock_product);
        return view('selling', [
            'products' => $products
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

}
