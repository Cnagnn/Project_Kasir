<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction; // Asumsi Anda punya model 'Transaction'
use App\Models\Product;     // Asumsi Anda punya model 'Product'
use App\Models\Category;    // Asumsi Anda punya model 'Category'
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data ringkasan.
     */
    public function index()
    {
        // === Pengaturan Dasar ===
        // 1. Mengambil data pengguna yang sedang login
        $user = Auth::user();

        // 2. Menyiapkan tanggal hari ini dan kemarin untuk perbandingan
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // === Data untuk Kartu KPI (Key Performance Indicator) ===

        // 1. Penjualan Hari Ini (Rp)
        $penjualanHariIni = Transaction::whereDate('created_at', $today)->sum('total_payment'); // Ganti 'total_harga' sesuai nama kolom Anda
        $penjualanKemarin = Transaction::whereDate('created_at', $yesterday)->sum('total_payment');
        
        // Hitung persentase perubahan penjualan
        $persentasePenjualan = 0;
        if ($penjualanKemarin > 0) {
            // Rumus: ((Baru - Lama) / Lama) * 100
            $persentasePenjualan = (($penjualanHariIni - $penjualanKemarin) / $penjualanKemarin) * 100;
        } elseif ($penjualanHariIni > 0) {
            $persentasePenjualan = 100; // Jika kemarin 0 dan hari ini ada penjualan
        }

        // 2. Transaksi Hari Ini (Jumlah)
        $transaksiHariIni = Transaction::whereDate('created_at', $today)->count();
        $transaksiKemarin = Transaction::whereDate('created_at', $yesterday)->count();

        // Hitung persentase perubahan transaksi
        $persentaseTransaksi = 0;
        if ($transaksiKemarin > 0) {
            $persentaseTransaksi = (($transaksiHariIni - $transaksiKemarin) / $transaksiKemarin) * 100;
        } elseif ($transaksiHariIni > 0) {
            $persentaseTransaksi = 100;
        }

        // 3. Produk Aktif
        // Asumsi "Produk Aktif" adalah total produk yang ada di database
        $produkAktif = Product::count(); 
        // Asumsi "+12 barang" adalah produk yang baru ditambahkan hari ini
        $produkBaruHariIni = Product::whereDate('created_at', $today)->count(); 

        // 4. Kategori
        $totalKategori = Category::count();
        // Asumsi "+1 kategori" adalah kategori yang baru ditambahkan hari ini
        $kategoriBaruHariIni = Category::whereDate('created_at', $today)->count();

        // === Data untuk Tabel "Transaksi Terbaru" ===
        // Mengambil 5 transaksi terakhir
        $transaksiTerbaru = Transaction::latest()       // Mengurutkan berdasarkan 'created_at' (terbaru dulu)
                                        ->with('user') // Mengambil data relasi dengan "users"
                                        ->whereDate('created_at', $today)
                                        ->take(5)       // Ambil 5 data saja
                                        ->get();
                                      
        // dd($transaksiTerbaru);
        // === Mengirim Semua Data ke View ===
        // Kita akan mengirim semua variabel ini ke file 'dashboard.blade.php'
        return view('dashboard', compact(
            'user',
            'penjualanHariIni',
            'persentasePenjualan',
            'transaksiHariIni',
            'persentaseTransaksi',
            'produkAktif',
            'produkBaruHariIni',
            'totalKategori',
            'kategoriBaruHariIni',
            'transaksiTerbaru'
        ));
    }

    /**
     * Endpoint JSON untuk data grafik penjualan (harian & bulanan).
     */
    public function salesData()
    {
        $now = Carbon::now();

        // === Harian (Minggu ini: Senin - Minggu) ===
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $rawDaily = Transaction::selectRaw('DATE(created_at) as date, SUM(total_payment) as total')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        $dailyLabels = [];
        $dailyData = [];
        $labelMap = [
            'Monday' => 'Sen',
            'Tuesday' => 'Sel',
            'Wednesday' => 'Rab',
            'Thursday' => 'Kam',
            'Friday' => 'Jum',
            'Saturday' => 'Sab',
            'Sunday' => 'Min'
        ];
        for ($d = $weekStart->copy(); $d->lte($weekEnd); $d->addDay()) {
            $dailyLabels[] = $labelMap[$d->format('l')] ?? $d->format('d');
            $key = $d->format('Y-m-d');
            // Isi data hanya sampai hari ini, hari mendatang = 0
            if ($d->gt($now)) {
                $dailyData[] = 0;
            } else {
                $dailyData[] = (int)($rawDaily[$key] ?? 0);
            }
        }

        // === Bulanan (tahun berjalan) ===
        $year = $now->year;
        $rawMonthly = Transaction::selectRaw('MONTH(created_at) as month, SUM(total_payment) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        $monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = (int)($rawMonthly[$m] ?? 0);
        }

        return response()->json([
            'daily' => [
                'labels' => $dailyLabels,
                'data' => $dailyData,
            ],
            'monthly' => [
                'labels' => $monthLabels,
                'data' => $monthlyData,
            ],
            'currency' => 'IDR'
        ]);
    }

    /**
     * Endpoint JSON untuk KPI berdasarkan timeframe: day, week, month.
     */
    public function metrics(Request $request)
    {
        $timeframe = $request->query('timeframe', 'day');
        $now = Carbon::now();
        $start = null; $end = null; $label = '';

        switch ($timeframe) {
            case 'week':
                $start = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $end = $now->copy()->endOfDay(); // hanya sampai hari ini
                $label = 'Minggu ini';
                break;
            case 'month':
                $start = $now->copy()->startOfMonth()->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Bulan ini';
                break;
            case 'day':
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Hari ini';
                $timeframe = 'day';
                break;
        }

        $totalPenjualan = Transaction::whereBetween('created_at', [$start, $end])->sum('total_payment');
        $jumlahTransaksi = Transaction::whereBetween('created_at', [$start, $end])->count();

        return response()->json([
            'timeframe' => $timeframe,
            'label' => $label,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'penjualan' => (int)$totalPenjualan,
            'transaksi' => (int)$jumlahTransaksi,
        ]);
    }

    /**
     * Data kategori terlaris (top 5) berdasarkan total subtotal transaksi.
     */
    public function categoryData()
    {
        $rows = DB::table('transaction_details')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category, SUM(transaction_details.subtotal) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        // Filter out categories with zero sales just in case
        $filtered = $rows->filter(function($row){ return (int)$row->total > 0; });
        
        $labels = $filtered->pluck('category');
        $totals = $filtered->pluck('total')->map(fn($v) => (int)$v);
        
        // Hitung total keseluruhan untuk persentase
        $grandTotal = $totals->sum();
        
        // Hitung persentase setiap kategori
        $data = $totals->map(function($value) use ($grandTotal) {
            return $grandTotal > 0 ? round(($value / $grandTotal) * 100, 1) : 0;
        });

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * Data produk terjual per bulan: quantity dan nominal.
     */
    public function salesProductData()
    {
        $now = Carbon::now();
        $year = $now->year;

        // Data Quantity - Rincian per produk
        $rawQuantityProducts = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->selectRaw('products.name as product_name, MONTH(transactions.created_at) as month, SUM(transaction_details.quantity) as total')
            ->whereYear('transactions.created_at', $year)
            ->groupBy('products.name', 'month')
            ->orderBy('products.name')
            ->orderBy('month')
            ->get();

        // Group by product
        $productQuantityMap = [];
        foreach ($rawQuantityProducts as $row) {
            if (!isset($productQuantityMap[$row->product_name])) {
                $productQuantityMap[$row->product_name] = array_fill(1, 12, 0);
            }
            $productQuantityMap[$row->product_name][$row->month] = (int)$row->total;
        }

        // Ambil top 10 produk berdasarkan total penjualan sepanjang tahun
        $productTotals = [];
        foreach ($productQuantityMap as $productName => $monthData) {
            $productTotals[$productName] = array_sum($monthData);
        }
        arsort($productTotals);
        $topProducts = array_slice(array_keys($productTotals), 0, 10, true);

        // Format data untuk chart (top 10 products)
        $quantityDatasets = [];
        $colors = [
            'rgba(99, 102, 241, 1)', // indigo
            'rgba(16, 185, 129, 1)', // green
            'rgba(245, 158, 11, 1)', // orange
            'rgba(239, 68, 68, 1)',  // red
            'rgba(14, 165, 233, 1)', // blue
            'rgba(168, 85, 247, 1)', // purple
            'rgba(236, 72, 153, 1)', // pink
            'rgba(20, 184, 166, 1)', // teal
            'rgba(251, 146, 60, 1)', // amber
            'rgba(132, 204, 22, 1)'  // lime
        ];

        $colorIndex = 0;
        foreach ($topProducts as $productName) {
            $monthData = $productQuantityMap[$productName];
            $dataArray = [];
            for ($m = 1; $m <= 12; $m++) {
                $dataArray[] = $monthData[$m];
            }
            
            $color = $colors[$colorIndex % count($colors)];
            $quantityDatasets[] = [
                'label' => $productName,
                'data' => $dataArray,
                'borderColor' => $color,
                'backgroundColor' => str_replace('1)', '0.1)', $color),
                'tension' => 0.4,
                'fill' => false,
                'pointRadius' => 3,
                'pointHoverRadius' => 6,
                'pointBorderWidth' => 2,
                'pointBackgroundColor' => '#fff',
                'pointBorderColor' => $color
            ];
            $colorIndex++;
        }

        // Data Nominal (total subtotal per bulan)
        $rawNominal = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->selectRaw('MONTH(transactions.created_at) as month, SUM(transaction_details.subtotal) as total')
            ->whereYear('transactions.created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        $monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $nominalData = [];

        for ($m = 1; $m <= 12; $m++) {
            $nominalData[] = (int)($rawNominal[$m] ?? 0);
        }

        return response()->json([
            'quantity' => [
                'labels' => $monthLabels,
                'datasets' => $quantityDatasets,
            ],
            'nominal' => [
                'labels' => $monthLabels,
                'data' => $nominalData,
            ]
        ]);
    }
}