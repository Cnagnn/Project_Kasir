<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionHistoryController extends Controller
{
    //
    public function index()
    {

        return view('transactionHistory');

    }

    public function getTransactionHistory(Request $request)
    {

        // 1. CEK APAKAH REQUEST DATANG DARI AJAX (JQUERY)
        if ($request->ajax()) {
            
            $dataTransaksi = [];

            // Cek input tanggal
            if ($request->has(['tanggal_mulai', 'tanggal_akhir'])) {
                
                $mulai = Carbon::parse($request->tanggal_mulai)->startOfDay(); 
                $akhir = Carbon::parse($request->tanggal_akhir)->endOfDay();

                // Query Database
                // Menggunakan with('user') agar relasi user terbawa ke JSON
                $dataTransaksi = Transaction::with('user')
                                ->whereBetween('created_at', [$mulai, $akhir])
                                ->orderBy('created_at', 'desc') // Opsional: urutkan dari yang terbaru
                                ->get();
            }

            // PENTING: Return response JSON, bukan View
            return response()->json([
                'status' => 'success',
                'data' => $dataTransaksi
            ]);
        }

    }

    public function detail($id)
    {
        $transaction = Transaction::with(['user', 'details.product'])->findOrFail($id);
        return view('transactionHistory_detail', compact('transaction'));
    }

    public function updateDetail(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_buy_price' => 'required|numeric|min:0',
            'product_sell_price' => 'required|numeric|min:0',
        ]);

        $detail = \App\Models\TransactionDetail::findOrFail($id);
        
        $detail->product_id = $request->product_id;
        $detail->quantity = $request->quantity;
        $detail->product_buy_price = $request->product_buy_price;
        $detail->product_sell_price = $request->product_sell_price;
        $detail->subtotal = $request->quantity * $request->product_sell_price;
        $detail->profit = ($request->product_sell_price - $request->product_buy_price) * $request->quantity;
        $detail->save();

        // Update total transaksi
        $transaction = $detail->transaction;
        $transaction->total_payment = $transaction->details->sum('subtotal');
        $transaction->save();

        return redirect()->back()->with('success', 'Detail transaksi berhasil diupdate!');
    }
}
