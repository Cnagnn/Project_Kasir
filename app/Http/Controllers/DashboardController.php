<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction; // Asumsi Anda punya model 'Transaction'
use App\Models\Product;     // Asumsi Anda punya model 'Product'
use App\Models\Category;    // Asumsi Anda punya model 'Category'
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
}