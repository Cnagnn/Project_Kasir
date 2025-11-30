<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
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

        // Ambil detail transaksi dalam rentang, agregasi per produk
        $details = TransactionDetail::with(['product'])
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        // Hitung pendapatan per produk (qty * price) dan total qty
        $perProduct = $details->groupBy('product_id')->map(function($rows){
            $name = optional($rows->first()->product)->name ?? 'Produk';
            $category = optional($rows->first()->product?->category)->name ?? '-';
            $qty = $rows->sum('quantity');
            $revenue = $rows->sum(function($r){
                // asumsi ada kolom price atau subtotal; fallback subtotal
                $price = $r->price ?? 0;
                $subtotal = $r->subtotal ?? ($price * ($r->quantity ?? 0));
                return (int)$subtotal;
            });
            return [
                'name' => $name,
                'category' => $category,
                'qty' => (int)$qty,
                'revenue' => (int)$revenue,
            ];
        })->values();

        $grandQty = $perProduct->sum('qty');
        $grandRevenue = $perProduct->sum('revenue');

        $pdf = Pdf::loadView('reports.product_revenue_pdf', [
            'rows' => $perProduct,
            'grandQty' => $grandQty,
            'grandRevenue' => $grandRevenue,
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
