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
            ->limit(5)
            ->get();

        $labels = $rows->pluck('category');
        $data = $rows->pluck('total')->map(fn($v) => (int)$v);

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}