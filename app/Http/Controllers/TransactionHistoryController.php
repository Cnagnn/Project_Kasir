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

    public function detail(Request $request, $id)
    {
        $transaction = Transaction::with(['user', 'details.product' => function($query) {
            $query->withTrashed();
        }])->findOrFail($id);
        
        // Check if it's an AJAX request
        if ($request->ajax()) {
            return view('transactionHistory_detail_content', compact('transaction'));
        }

        // Regular request (fallback)
        return view('transactionHistory_detail', compact('transaction'));
    }

    public function updateDetail(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_buy_price' => 'required|numeric|min:0',
            'product_sell_price' => 'required|numeric|min:0',
        ], [
            'product_id.required' => 'Produk harus dipilih.',
            'product_id.exists' => 'Produk tidak valid.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal 1.',
            'product_buy_price.required' => 'Harga beli harus diisi.',
            'product_buy_price.numeric' => 'Harga beli harus berupa angka.',
            'product_buy_price.min' => 'Harga beli tidak boleh negatif.',
            'product_sell_price.required' => 'Harga jual harus diisi.',
            'product_sell_price.numeric' => 'Harga jual harus berupa angka.',
            'product_sell_price.min' => 'Harga jual tidak boleh negatif.',
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

        // Jika AJAX request, return JSON dengan flag print
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Detail transaksi berhasil diupdate!',
                'show_receipt' => true,
                'transaction_id' => $transaction->id
            ]);
        }

        return redirect()->back()->with([
            'success' => 'Detail transaksi berhasil diupdate!',
            'show_receipt' => true,
            'transaction_id' => $transaction->id
        ]);
    }

    public function deleteDetail($id)
    {
        $detail = \App\Models\TransactionDetail::findOrFail($id);
        $transaction = $detail->transaction;
        
        // Hapus detail
        $detail->delete();
        
        // Update total transaksi
        $transaction->total_payment = $transaction->details->sum('subtotal');
        $transaction->save();
        
        // Jika AJAX request, return JSON dengan flag print
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item transaksi berhasil dihapus!',
                'show_receipt' => true,
                'transaction_id' => $transaction->id
            ]);
        }
        
        return redirect()->back()->with([
            'success' => 'Item transaksi berhasil dihapus!',
            'show_receipt' => true,
            'transaction_id' => $transaction->id
        ]);
    }

    public function print($id)
    {
        $transaction = Transaction::with(['user', 'details.product' => function($query) {
            $query->withTrashed();
        }])->findOrFail($id);

        // Hitung subtotal tiap detail & total akhir
        $items = $transaction->details->map(function ($d) {
            return [
                'name' => optional($d->product)->name ?? 'Produk Terhapus',
                'qty' => $d->quantity,
                'price' => $d->product_sell_price,
                'subtotal' => $d->subtotal,
            ];
        });

        $total = $items->sum('subtotal');

        return view('receipt', [
            'transaction' => $transaction,
            'items' => $items,
            'total' => $total,
        ]);
    }
}
