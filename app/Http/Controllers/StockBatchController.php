<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductStockBatches;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class StockBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        // Validasi input dari modal 'Tambah Batch'
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'initial_stock' => 'required|integer|min:0',
            // Pastikan stok tersisa tidak lebih besar dari stok awal
            'remaining_stock' => 'required|integer|min:0|lte:initial_stock', 
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
        ]);

        ProductStockBatches::create([
            'product_id' => $request->product_id,
            'initial_stock' => $request->initial_stock,
            'remaining_stock' => $request->remaining_stock,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::back()->with('batch_add_success', 'Data Batch Berhasil Ditambah.');
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
    public function edit(Request $request, $id)
    {
        //
        // $request = ProductStockBatches::findOrFail($id);
        // dd($request);
        // Validasi input dari modal 'Edit Batch'
        $request->validate([
            'initial_stock' => 'required|integer|min:0',
            'remaining_stock' => 'required|integer|min:0|lte:initial_stock',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
        ]);

        $batch = ProductStockBatches::findOrFail($id);

        // $batch adalah model yang didapat otomatis oleh Laravel (Route Model Binding)
        $batch->update([
            'initial_stock' => $request->initial_stock,
            'remaining_stock' => $request->remaining_stock,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return Redirect::back()->with('batch_update_success', 'Data Batch Berhasil Diperbarui.');
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
    public function destroy(Request $request, $id)
    {
        $batch = ProductStockBatches::where('id',$id)->first();
        // dd($batch);
        $batch->delete();
        
        return redirect()->back()->with('batch_destroy_success', 'Data Batch Berhasil Dihapus.');
    }
}
