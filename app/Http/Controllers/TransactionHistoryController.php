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
}
