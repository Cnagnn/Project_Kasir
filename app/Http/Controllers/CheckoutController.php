<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Stock; // Sesuaikan nama model
use App\Models\Product;
use Midtrans\Config as MidtransConfig;
use Midtrans\CoreApi as MidtransCoreApi;

class CheckoutController extends Controller
{
    //

    public function process(Request $request)
    {
        $request->validate([
            'amount_paid'       => 'required|numeric|min:0',
            'payment_method'    => 'required|string|in:Tunai,QRIS',
        ]);

        $cart = session('cart', []);

        // dd($cart);
        if (empty($cart)) {
            // Kembali ke halaman kasir jika keranjang kosong
            return redirect()->route('selling.index')->with('error', 'Keranjang Anda kosong.'); 
        }

        $totalAmount = 0;
        foreach ($cart as $details) {
            // Perbaikan: Gunakan 'subtotal_sell' jika ada (Logika FIFO Baru)
            if (isset($details['subtotal_sell'])) {
                $totalAmount += $details['subtotal_sell'];
            } else {
                // Fallback untuk data lama (jika belum ada subtotal_sell)
                $totalAmount += $details['sell_price'] * $details['quantity'];
            }
        }

        $paymentMethod = $request->payment_method;

        try {
            switch ($paymentMethod) {
                case 'Tunai':
                    if ($request->amount_paid < $totalAmount) {
                         // Kembali ke halaman kasir (atau modal?) dengan error
                        return redirect()->route('selling.index')->with('error', 'Jumlah uang tunai tidak mencukupi!');
                    }
                    $transaction = $this->handleCashPayment($request, $cart, $totalAmount);
                    session()->forget('cart');
                     // Kembali ke halaman kasir setelah sukses
                    return redirect()->route('selling.index')->with('success', 'Transaksi Tunai #' . $transaction->invoice_number . ' berhasil!');

                case 'QRIS':
                    $response = $this->handleQrisPayment($cart, $totalAmount);
                    session()->forget('cart');
                     // Arahkan ke view 'tunggu pembayaran'
                    return view('payment_wait', [
                        'transaction' => $response['transaction'],
                        'qrCodeUrl' => $response['qr_code_url'],
                        'totalAmount' => $totalAmount
                    ]);
            }
        } catch (\Exception $e) {
            // Kembali ke halaman kasir jika ada error
            // Log::error('Checkout Error: ' . $e->getMessage()); // Opsional
            return redirect()->route('selling.index')->with('error', 'Transaksi Gagal! ' . $e->getMessage());
        }
    }

    /**
     * Menangani logika pembayaran TUNAI.
     * (Mengurangi stok & langsung simpan detail granular)
     */
    private function handleCashPayment(Request $request, $cart, $totalAmount)
    {
        return DB::transaction(function () use ($request, $cart, $totalAmount) {
            $transaction = Transaction::create([
                'invoice_number'      => 'INV-' . date('Ymd-His') . Auth::id(),
                'total_payment'       => $totalAmount,
                'payment_method'      => 'Tunai',
                'payment_status'      => 'Paid',
                'user_id'             => Auth::id(),
                'transaction_date'    => now(),
            ]);

            foreach ($cart as $productId => $item) {
                // $product = Product::where('id', $productId)->first();
                $quantityToSell = $item['quantity'];
                $batches = Stock::where('product_id', $productId)
                                       ->where('remaining_stock', '>', 0)
                                       ->orderBy('created_at', 'asc')
                                       ->lockForUpdate() // Kunci batch untuk mencegah race condition
                                       ->get();
                if ($batches->sum('remaining_stock') < $quantityToSell) {
                    throw new \Exception('Stok untuk "' . $item['name'] . '" tidak mencukupi.');
                }
                foreach ($batches as $batch) {
                    if ($quantityToSell <= 0) break;
                    $stockToTake = min($quantityToSell, $batch->remaining_stock);
                    
                    // Kurangi stok
                    $batch->decrement('remaining_stock', $stockToTake);
                    // dd([$item, $batch, $product]);
                    // Catat detail granular
                    $transaction->details()->create([
                        'product_id' => $productId,
                        'quantity'   => $stockToTake,
                        'product_sell_price'      => $batch->sell_price,
                        'product_buy_price'  => $batch->buy_price, // Pastikan nama kolom 'harga_beli' benar
                        'subtotal'   => $batch->sell_price * $stockToTake,
                        'profit'     => ($batch->sell_price - $batch->buy_price) * $stockToTake,
                    ]);
                    $quantityToSell -= $stockToTake;
                }
            }
            return $transaction;
        });
    }

    /**
     * Menangani logika pembayaran QRIS.
     * (Status PENDING, TIDAK kurangi stok, minta QR ke Midtrans)
     */
     private function handleQrisPayment($cart, $totalAmount)
    {
        $invoiceNumber = 'INV-' . date('Ymd-His') . Auth::id();
        
        // Buat transaksi PENDING di DB (tanpa kurangi stok)
        $transaction = DB::transaction(function () use ($cart, $totalAmount, $invoiceNumber) {
            $transaction = Transaction::create([
                'invoice_number'      => $invoiceNumber,
                'user_id'             => Auth::id(),
                'total_payment'        => $totalAmount,
                'payment_method'      => 'QRIS',
                'payment_status'      => 'Pending',
                'transaction_date'    => now(),
            ]);
            // Catat detail (tanpa stok & data finansial detail, itu tugas webhook)
            // foreach ($cart as $productId => $item) {
            //     $transaction->details()->create([
            //         'product_id' => $productId,
            //         'quantity'   => $item['quantity'],
            //         'price'      => $item['sell_price'],
            //         'subtotal'   => $item['sell_price'] * $item['quantity'],
            //     ]);
            // }
            return $transaction;
        });

        // Panggil Midtrans
        $midtransParams = [
            'payment_type' => 'qris',
            'transaction_details' => ['order_id' => $invoiceNumber, 'gross_amount' => $totalAmount],
            'customer_details' => [ // Ambil data user yang login
                'first_name' => Auth::user()->name ?? 'Pelanggan', 
                'email' => Auth::user()->email ?? 'customer@example.com',
                // 'phone' => Auth::user()->phone ?? '08123456789' // Jika ada
            ],
            // Tambahkan item_details untuk detail di Midtrans (Opsional tapi bagus)
            'item_details' => array_map(function($item, $id){
                return [
                    'id'       => $id,
                    'price'    => $item['sell_price'],
                    'quantity' => $item['quantity'],
                    'name'     => $item['name']
                ];
            }, $cart, array_keys($cart))
        ];
        // dd($midtransParams);
        $midtransResponse = MidtransCoreApi::charge($midtransParams);

        // Kembalikan data untuk view 'payment_wait'
        return ['transaction' => $transaction, 'qr_code_url' => $midtransResponse->actions[0]->url ?? $midtransResponse->qr_string]; // Ambil URL QR dari 'actions' jika ada (lebih baru)
    }
}
