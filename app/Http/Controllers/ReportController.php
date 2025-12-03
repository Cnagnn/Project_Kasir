<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.report');
    }

    public function printStock(Request $request)
    {
        // Ambil produk beserta stok dan kategori
        $products = Product::with(['stock', 'category'])->get()->map(function ($product) {
            $totalRemaining = $product->stock->sum('remaining_stock');
            // Ambil harga jual terakhir dari record stok terbaru
            $latestStock = $product->stock->sortByDesc('created_at')->first();
            $sellPrice = $latestStock?->sell_price ?? 0;
            return [
                'name' => $product->name ?? 'Produk',
                'category' => $product->category->name ?? '-',
                'total_remaining' => $totalRemaining,
                'sell_price' => $sellPrice,
            ];
        });

        // Render view untuk PDF
        $pdf = Pdf::loadView('reports.stock_pdf', [
            'rows' => $products,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        // Format nama file: Laporan Stock per 30 November 2025 - 14.30 WIB
        $filename = 'Laporan Stock per ' . now()->translatedFormat('d F Y') . ' - ' . now()->format('H.i') . ' WIB.pdf';

        // Return dengan output() dan header inline untuk preview di browser
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function printInvoiceRevenue(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        // Validasi sederhana
        if (!$start || !$end) {
            return response('Tanggal mulai dan selesai wajib diisi.', 422);
        }

        $startDate = \Carbon\Carbon::parse($start)->startOfDay();
        $endDate = \Carbon\Carbon::parse($end)->endOfDay();

        // Ambil transaksi dalam rentang tanggal
        $invoices = Transaction::with(['user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($trx) {
                return [
                    'invoice' => $trx->invoice_number ?? $trx->id,
                    'operator' => $trx->user->name ?? '-',
                    'total' => (int)($trx->total_payment ?? 0),
                    'date' => $trx->created_at,
                ];
            });

        $grandTotal = $invoices->sum('total');

        $pdf = Pdf::loadView('reports.invoice_revenue_pdf', [
            'rows' => $invoices,
            'grandTotal' => $grandTotal,
            'rangeLabel' => $startDate->translatedFormat('d F Y'). ' - ' . $endDate->translatedFormat('d F Y'),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan Pendapatan per Invoice ' . $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y') . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function printProductRevenue(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        if (!$start || !$end) {
            return response('Tanggal mulai dan selesai wajib diisi.', 422);
        }

        $startDate = \Carbon\Carbon::parse($start)->startOfDay();
        $endDate = \Carbon\Carbon::parse($end)->endOfDay();

        // Ambil detail transaksi dalam rentang + relasi produk & kategori
        $details = TransactionDetail::with(['product.category', 'transaction'])
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        // Agregasi per produk: qty total, rata-rata HPP (buy price), rata-rata harga jual, total profit
        $perProduct = $details->groupBy('product_id')->map(function($rows){
            $first = $rows->first();
            $name = optional($first->product)->name ?? 'Produk';
            $category = optional($first->product?->category)->name ?? '-';
            $qty = (int)$rows->sum('quantity');
            // Total cost (HPP agregat) = sum(product_buy_price * quantity)
            $totalHpp = (int)$rows->sum(fn($r) => ($r->product_buy_price ?? 0) * ($r->quantity ?? 0));
            // Total revenue (harga jual agregat) = sum(product_sell_price * quantity) or subtotal
            $totalRevenue = (int)$rows->sum(fn($r) => ($r->product_sell_price ?? 0) * ($r->quantity ?? 0));
            // Rata-rata per-unit (untuk tampilan kolom) – fallback 0 bila tidak ada qty
            $avgBuy = $qty > 0 ? (int)round($totalHpp / $qty) : 0;
            $avgSell = $qty > 0 ? (int)round($totalRevenue / $qty) : 0;
            // Profit total (gunakan field profit jika tersedia, kalau tidak hitung dari selisih total)
            $profitTotal = (int)$rows->sum('profit');
            if ($profitTotal === 0) {
                $profitTotal = $totalRevenue - $totalHpp;
            }
            return [
                'name' => $name,
                'category' => $category,
                'qty' => $qty,
                // Kolom buy_price & sell_price di view diinterpretasi sebagai per-unit
                'buy_price' => $avgBuy,
                'sell_price' => $avgSell,
                'profit' => $profitTotal,
                // Simpan juga total jika nanti perlu dipakai (tidak dipakai di view saat ini)
                'total_hpp' => $totalHpp,
                'total_revenue' => $totalRevenue,
            ];
        })->values();

        // Grand totals
        $grandQty = $perProduct->sum('qty');
        $grandBuyPrice = $details->sum(fn($d) => ($d->product_buy_price ?? 0) * ($d->quantity ?? 0)); // total HPP
        $grandSellPrice = $details->sum(fn($d) => ($d->product_sell_price ?? 0) * ($d->quantity ?? 0)); // total revenue
        $grandProfit = $details->sum('profit');
        if ($grandProfit == 0) {
            $grandProfit = $grandSellPrice - $grandBuyPrice;
        }

        $pdf = Pdf::loadView('reports.product_revenue_pdf', [
            'rows' => $perProduct,
            'grandQty' => $grandQty,
            // Kirim total agregat untuk ditampilkan di footer
            'grandBuyPrice' => $grandBuyPrice,
            'grandSellPrice' => $grandSellPrice,
            'grandProfit' => $grandProfit,
            'rangeLabel' => $startDate->translatedFormat('d F Y'). ' - ' . $endDate->translatedFormat('d F Y'),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan Pendapatan per Produk ' . $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y') . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
