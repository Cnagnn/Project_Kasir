<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('products')->insert([
        //     'name' => "Indomie Goreng",
        //     'category_id' => 1,
        //     'sell_price' => 5000,
        //     'image' => 'image.jpg',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('stocks')->insert([
        //     'product_id' => 1,
        //     'initial_stock' => 10,
        //     'remaining_stock' => 10,
        //     'buy_price' => 2000,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('stocks')->insert([
        //     'product_id' => 1,
        //     'initial_stock' => 10,
        //     'remaining_stock' => 10,
        //     'buy_price' => 2000,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('products')->insert([
        //     'name' => "Teh Botol",
        //     'category_id' => 2,
        //     'sell_price' => 5000,
        //     'image' => 'image.jpg',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('stocks')->insert([
        //     'product_id' => 2,
        //     'initial_stock' => 10,
        //     'remaining_stock' => 10,
        //     'buy_price' => 3000,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // ID Kategori
        $catMakananInstan = 1;
        $catMinuman = 2;
        $catBumbu = 3;
        $catRt = 4;
        $catMandi = 5;
        $catJajanan = 6;
        $catKopiTeh = 7;

        // Data mentah produk dengan harga jual manual dan harga beli yang sudah dibulatkan manual
        $productsData = [
            // --- Sesuai permintaan ---
            ['name' => 'Indomie Goreng', 'cat_id' => $catMakananInstan, 'price' => 3000, 'batches' => [
                ['stock' => 100, 'buy' => 2500],
            ]],
            ['name' => 'Teh Botol Sosro Kotak 250ml', 'cat_id' => $catMinuman, 'price' => 4000, 'batches' => [
                ['stock' => 20, 'buy' => 2900],
                ['stock' => 20, 'buy' => 3000],
                ['stock' => 10, 'buy' => 3100], // 3050 -> 3100
            ]],
            ['name' => 'Indomie Soto Mie', 'cat_id' => $catMakananInstan, 'price' => 3000, 'batches' => [
                ['stock' => 40, 'buy' => 2500], // 2410 -> 2500
                ['stock' => 30, 'buy' => 2500],
                ['stock' => 30, 'buy' => 2600], // 2550 -> 2600
            ]],
            
            // --- Variasi lainnya ---
            ['name' => 'Aqua 600ml', 'cat_id' => $catMinuman, 'price' => 3500, 'batches' => [
                ['stock' => 40, 'buy' => 2800], // 2720 -> 2800
                ['stock' => 40, 'buy' => 2800],
            ]],
            ['name' => 'Kecap Bango Manis 220ml', 'cat_id' => $catBumbu, 'price' => 16000, 'batches' => [
                ['stock' => 15, 'buy' => 14100], // 14010 -> 14100
                ['stock' => 15, 'buy' => 14100],
            ]],
            ['name' => 'Sunlight Cuci Piring 755ml', 'cat_id' => $catRt, 'price' => 14000, 'batches' => [
                ['stock' => 40, 'buy' => 12000], // 11950 -> 12000
            ]],
            ['name' => 'Sabun Lifebuoy Batang Merah', 'cat_id' => $catMandi, 'price' => 4000, 'batches' => [
                ['stock' => 30, 'buy' => 2900],
                ['stock' => 30, 'buy' => 3000],
            ]],
            ['name' => 'Chitato Sapi Panggang 68gr', 'cat_id' => $catJajanan, 'price' => 11000, 'batches' => [
                ['stock' => 50, 'buy' => 9000], // 8910 -> 9000
            ]],
            ['name' => 'Kopi Kapal Api Special 165gr', 'cat_id' => $catKopiTeh, 'price' => 14000, 'batches' => [
                ['stock' => 10, 'buy' => 11800],
                ['stock' => 10, 'buy' => 12000],
                ['stock' => 10, 'buy' => 12100], // 12050 -> 12100
            ]],
            ['name' => 'Mie Sedap Goreng', 'cat_id' => $catMakananInstan, 'price' => 3000, 'batches' => [
                ['stock' => 100, 'buy' => 2500],
            ]],
            ['name' => 'Le Minerale 600ml', 'cat_id' => $catMinuman, 'price' => 3500, 'batches' => [
                ['stock' => 80, 'buy' => 2800], // 2770 -> 2800
            ]],
            ['name' => 'Masako Sapi 100gr', 'cat_id' => $catBumbu, 'price' => 5000, 'batches' => [
                ['stock' => 30, 'buy' => 3900],
                ['stock' => 40, 'buy' => 4000],
            ]],
            ['name' => 'Rinso Deterjen Bubuk 770gr', 'cat_id' => $catRt, 'price' => 22000, 'batches' => [
                ['stock' => 25, 'buy' => 19000], // 18910 -> 19000
            ]],
            ['name' => 'Pepsodent Pasta Gigi 190gr', 'cat_id' => $catMandi, 'price' => 14000, 'batches' => [
                ['stock' => 20, 'buy' => 10500],
                ['stock' => 20, 'buy' => 11000],
            ]],
            ['name' => 'Lays Rumput Laut 68gr', 'cat_id' => $catJajanan, 'price' => 11000, 'batches' => [
                ['stock' => 50, 'buy' => 9000], // 8950 -> 9000
            ]],
            ['name' => 'Kopi ABC Susu Sachet', 'cat_id' => $catKopiTeh, 'price' => 1500, 'batches' => [
                ['stock' => 100, 'buy' => 1000], // 950 -> 1000
                ['stock' => 50, 'buy' => 1000],
            ]],
            ['name' => 'Sarden ABC Tomat', 'cat_id' => $catMakananInstan, 'price' => 12000, 'batches' => [
                ['stock' => 30, 'buy' => 9500],
            ]],
            ['name' => 'Susu Ultra Coklat 250ml', 'cat_id' => $catMinuman, 'price' => 6500, 'batches' => [
                ['stock' => 20, 'buy' => 4900],
                ['stock' => 20, 'buy' => 5000],
            ]],
            ['name' => 'Saus Sambal ABC Asli 135ml', 'cat_id' => $catBumbu, 'price' => 8000, 'batches' => [
                ['stock' => 50, 'buy' => 6500],
            ]],
            ['name' => 'Baygon Semprot 600ml', 'cat_id' => $catRt, 'price' => 35000, 'batches' => [
                ['stock' => 10, 'buy' => 29000],
                ['stock' => 10, 'buy' => 30000],
            ]],
            ['name' => 'Shampoo Sunsilk Hitam 170ml', 'cat_id' => $catMandi, 'price' => 20000, 'batches' => [
                ['stock' => 25, 'buy' => 17000],
            ]],
            ['name' => 'Beng-Beng Coklat', 'cat_id' => $catJajanan, 'price' => 2500, 'batches' => [
                ['stock' => 50, 'buy' => 1700],
                ['stock' => 50, 'buy' => 1800],
            ]],
            ['name' => 'Teh Celup Sosro (isi 30)', 'cat_id' => $catKopiTeh, 'price' => 10000, 'batches' => [
                ['stock' => 30, 'buy' => 8000],
            ]],
            ['name' => 'Super Bubur Instan Ayam', 'cat_id' => $catMakananInstan, 'price' => 5000, 'batches' => [
                ['stock' => 40, 'buy' => 4000],
            ]],
            ['name' => 'Pocari Sweat 500ml', 'cat_id' => $catMinuman, 'price' => 7000, 'batches' => [
                ['stock' => 30, 'buy' => 5500],
            ]],
            ['name' => 'Tepung Terigu Segitiga Biru 1kg', 'cat_id' => $catBumbu, 'price' => 13000, 'batches' => [
                ['stock' => 10, 'buy' => 10500],
                ['stock' => 10, 'buy' => 11000],
            ]],
            ['name' => 'Tisu Wajah Paseo 250s', 'cat_id' => $catRt, 'price' => 15000, 'batches' => [
                ['stock' => 30, 'buy' => 12000],
            ]],
            ['name' => 'Sabun Dettol Original', 'cat_id' => $catMandi, 'price' => 5000, 'batches' => [
                ['stock' => 60, 'buy' => 4000],
            ]],
            ['name' => 'Oreo Original 133gr', 'cat_id' => $catJajanan, 'price' => 10000, 'batches' => [
                ['stock' => 20, 'buy' => 7900],
                ['stock' => 20, 'buy' => 8000],
            ]],
            ['name' => 'Kopi Good Day Mocacinno Sachet', 'cat_id' => $catKopiTeh, 'price' => 2000, 'batches' => [
                ['stock' => 150, 'buy' => 1500],
            ]],
            ['name' => 'Indomie Goreng Jumbo', 'cat_id' => $catMakananInstan, 'price' => 4500, 'batches' => [
                ['stock' => 50, 'buy' => 3800],
            ]],
            ['name' => 'Teh Pucuk Harum 350ml', 'cat_id' => $catMinuman, 'price' => 3500, 'batches' => [
                ['stock' => 70, 'buy' => 2800],
            ]],
            ['name' => 'Santan Kara 65ml', 'cat_id' => $catBumbu, 'price' => 3000, 'batches' => [
                ['stock' => 30, 'buy' => 2100],
                ['stock' => 30, 'buy' => 2200],
            ]],
            ['name' => 'Daia Bubuk Putih 850gr', 'cat_id' => $catRt, 'price' => 19000, 'batches' => [
                ['stock' => 25, 'buy' => 16000],
            ]],
            ['name' => 'Lifebuoy Sabun Cair 450ml', 'cat_id' => $catMandi, 'price' => 22000, 'batches' => [
                ['stock' => 20, 'buy' => 18000],
            ]],
            ['name' => 'Silverqueen 62gr', 'cat_id' => $catJajanan, 'price' => 13000, 'batches' => [
                ['stock' => 10, 'buy' => 9900],
                ['stock' => 10, 'buy' => 10000],
                ['stock' => 10, 'buy' => 10100],
            ]],
            ['name' => 'Teh Celup Sariwangi (isi 25)', 'cat_id' => $catKopiTeh, 'price' => 9000, 'batches' => [
                ['stock' => 30, 'buy' => 7000],
            ]],
            ['name' => 'Pop Mie Gede Ayam', 'cat_id' => $catMakananInstan, 'price' => 6000, 'batches' => [
                ['stock' => 40, 'buy' => 5000],
            ]],
            ['name' => 'Coca-Cola Kaleng 330ml', 'cat_id' => $catMinuman, 'price' => 7000, 'batches' => [
                ['stock' => 30, 'buy' => 5500],
            ]],
            ['name' => 'Gula Pasir Gulaku 1kg', 'cat_id' => $catBumbu, 'price' => 16000, 'batches' => [
                ['stock' => 25, 'buy' => 14000],
                ['stock' => 25, 'buy' => 14500],
            ]],
            ['name' => 'So Klin Pewangi Pakaian 900ml', 'cat_id' => $catRt, 'price' => 12000, 'batches' => [
                ['stock' => 30, 'buy' => 10000],
            ]],
            ['name' => 'Shampoo Pantene Rontok 135ml', 'cat_id' => $catMandi, 'price' => 23000, 'batches' => [
                ['stock' => 20, 'buy' => 19000],
            ]],
            ['name' => 'Taro Net 36gr', 'cat_id' => $catJajanan, 'price' => 6000, 'batches' => [
                ['stock' => 50, 'buy' => 4500],
            ]],
            ['name' => 'Luwak White Koffie Sachet', 'cat_id' => $catKopiTeh, 'price' => 1500, 'batches' => [
                ['stock' => 150, 'buy' => 1000],
            ]],
            ['name' => 'Indomie Kari Ayam', 'cat_id' => $catMakananInstan, 'price' => 3000, 'batches' => [
                ['stock' => 100, 'buy' => 2500],
            ]],
            ['name' => 'Susu Bendera Coklat 200ml', 'cat_id' => $catMinuman, 'price' => 5000, 'batches' => [
                ['stock' => 40, 'buy' => 4000],
            ]],
            ['name' => 'Minyak Goreng Bimoli 1L', 'cat_id' => $catBumbu, 'price' => 18000, 'batches' => [
                ['stock' => 15, 'buy' => 15500],
                ['stock' => 15, 'buy' => 16000],
            ]],
            ['name' => 'Hit Obat Nyamuk Bakar', 'cat_id' => $catRt, 'price' => 2000, 'batches' => [
                ['stock' => 50, 'buy' => 1500],
            ]],
            ['name' => 'Sikat Gigi Formula', 'cat_id' => $catMandi, 'price' => 5000, 'batches' => [
                ['stock' => 50, 'buy' => 3500],
            ]],
            ['name' => 'Roma Biskuit Kelapa 300gr', 'cat_id' => $catJajanan, 'price' => 12000, 'batches' => [
                ['stock' => 20, 'buy' => 9500],
            ]],
            ['name' => 'Nescafe Classic Sachet', 'cat_id' => $catKopiTeh, 'price' => 1000, 'batches' => [
                ['stock' => 100, 'buy' => 700],
            ]],
            ['name' => 'Sarimi Isi 2 Goreng', 'cat_id' => $catMakananInstan, 'price' => 4000, 'batches' => [
                ['stock' => 50, 'buy' => 3300],
            ]],
            ['name' => 'Fanta Strawberry 330ml', 'cat_id' => $catMinuman, 'price' => 7000, 'batches' => [
                ['stock' => 30, 'buy' => 5500],
            ]],
            ['name' => 'Royco Ayam 100gr', 'cat_id' => $catBumbu, 'price' => 5000, 'batches' => [
                ['stock' => 70, 'buy' => 4000],
            ]],
            ['name' => 'Mama Lemon 780ml', 'cat_id' => $catRt, 'price' => 13500, 'batches' => [
                ['stock' => 40, 'buy' => 11500],
            ]],
            ['name' => 'Sabun Shinzui Batang', 'cat_id' => $catMandi, 'price' => 4500, 'batches' => [
                ['stock' => 60, 'buy' => 3500],
            ]],
            ['name' => 'Sari Roti Tawar', 'cat_id' => $catJajanan, 'price' => 15000, 'batches' => [
                ['stock' => 15, 'buy' => 13000],
            ]],
            ['name' => 'Kornet Sapi Pronas', 'cat_id' => $catMakananInstan, 'price' => 25000, 'batches' => [
                ['stock' => 20, 'buy' => 22000],
            ]],
            ['name' => 'Aqua 1500ml', 'cat_id' => $catMinuman, 'price' => 6000, 'batches' => [
                ['stock' => 30, 'buy' => 5000],
            ]],
            ['name' => 'Saus Tomat Indofood 135ml', 'cat_id' => $catBumbu, 'price' => 7500, 'batches' => [
                ['stock' => 50, 'buy' => 6000],
            ]],
            ['name' => 'Molto Pelembut Pakaian 820ml', 'cat_id' => $catRt, 'price' => 15000, 'batches' => [
                ['stock' => 30, 'buy' => 12500],
            ]],
            ['name' => 'Shampoo Clear Menthol 160ml', 'cat_id' => $catMandi, 'price' => 24000, 'batches' => [
                ['stock' => 20, 'buy' => 20000],
            ]],
            ['name' => 'Permen Kopiko', 'cat_id' => $catJajanan, 'price' => 500, 'batches' => [
                ['stock' => 200, 'buy' => 300],
            ]],
            ['name' => 'Indomie Ayam Bawang', 'cat_id' => $catMakananInstan, 'price' => 3000, 'batches' => [
                ['stock' => 100, 'buy' => 2500],
            ]],
            ['name' => 'Susu Indomilk Coklat 190ml', 'cat_id' => $catMinuman, 'price' => 4500, 'batches' => [
                ['stock' => 40, 'buy' => 3500],
            ]],
            ['name' => 'Sasa MSG 100gr', 'cat_id' => $catBumbu, 'price' => 4000, 'batches' => [
                ['stock' => 50, 'buy' => 3000],
            ]],
            ['name' => 'Lampu Phillips LED 10W', 'cat_id' => $catRt, 'price' => 25000, 'batches' => [
                ['stock' => 15, 'buy' => 20000],
            ]],
            ['name' => 'Close Up Pasta Gigi 160gr', 'cat_id' => $catMandi, 'price' => 13000, 'batches' => [
                ['stock' => 40, 'buy' => 10000],
            ]],
            ['name' => 'Good Time Chocochip', 'cat_id' => $catJajanan, 'price' => 7000, 'batches' => [
                ['stock' => 30, 'buy' => 5500],
            ]],
            ['name' => 'Mie Sedap Soto', 'cat_id' => $catMakananInstan, 'price' => 3000, 'batches' => [
                ['stock' => 100, 'buy' => 2500],
            ]],
            ['name' => 'NutriSari Jeruk Peras', 'cat_id' => $catMinuman, 'price' => 1500, 'batches' => [
                ['stock' => 100, 'buy' => 1000],
            ]],
            ['name' => 'Garam Cap Kapal 250gr', 'cat_id' => $catBumbu, 'price' => 3000, 'batches' => [
                ['stock' => 50, 'buy' => 2000],
            ]],
            ['name' => 'Autan Lotion Nyamuk', 'cat_id' => $catRt, 'price' => 6000, 'batches' => [
                ['stock' => 40, 'buy' => 4500],
            ]],
            ['name' => 'Biore Sabun Cair 450ml', 'cat_id' => $catMandi, 'price' => 25000, 'batches' => [
                ['stock' => 20, 'buy' => 21000],
            ]],
            ['name' => 'Permen Kiss Mint', 'cat_id' => $catJajanan, 'price' => 6000, 'batches' => [
                ['stock' => 30, 'buy' => 4500],
            ]],
            ['name' => 'Sarden Maya Saus Cabe', 'cat_id' => $catMakananInstan, 'price' => 11000, 'batches' => [
                ['stock' => 30, 'buy' => 9000],
            ]],
            ['name' => 'Mizone Isotonic 500ml', 'cat_id' => $catMinuman, 'price' => 5000, 'batches' => [
                ['stock' => 30, 'buy' => 4000],
            ]],
            ['name' => 'Blue Band Margarin 200gr', 'cat_id' => $catBumbu, 'price' => 10000, 'batches' => [
                ['stock' => 20, 'buy' => 8500],
            ]],
            ['name' => 'Baterai ABC Alkaline AA (isi 2)', 'cat_id' => $catRt, 'price' => 10000, 'batches' => [
                ['stock' => 25, 'buy' => 8000],
            ]],
            ['name' => 'Shampoo Rejoice 150ml', 'cat_id' => $catMandi, 'price' => 19000, 'batches' => [
                ['stock' => 20, 'buy' => 16000],
            ]],
            ['name' => 'Sari Roti Coklat', 'cat_id' => $catJajanan, 'price' => 5000, 'batches' => [
                ['stock' => 30, 'buy' => 4000],
            ]],
            ['name' => 'Pop Mie Gede Baso', 'cat_id' => $catMakananInstan, 'price' => 6000, 'batches' => [
                ['stock' => 40, 'buy' => 5000],
            ]],
            ['name' => 'Sprite 330ml', 'cat_id' => $catMinuman, 'price' => 7000, 'batches' => [
                ['stock' => 30, 'buy' => 5500],
            ]],
            ['name' => 'Tepung Beras Rose Brand 500gr', 'cat_id' => $catBumbu, 'price' => 7000, 'batches' => [
                ['stock' => 20, 'buy' => 5500],
            ]],
            ['name' => 'Korek Api Gas', 'cat_id' => $catRt, 'price' => 3000, 'batches' => [
                ['stock' => 50, 'buy' => 2000],
            ]],
            ['name' => 'Malkist Abon', 'cat_id' => $catJajanan, 'price' => 8000, 'batches' => [
                ['stock' => 30, 'buy' => 6500],
            ]],
            ['name' => 'Mie Sedap Kari Spesial', 'cat_id' => $catMakananInstan, 'price' => 3000, 'batches' => [
                ['stock' => 100, 'buy' => 2500],
            ]],
            ['name' => 'Susu Ultra Full Cream 250ml', 'cat_id' => $catMinuman, 'price' => 6500, 'batches' => [
                ['stock' => 40, 'buy' => 5000],
            ]],
            ['name' => 'Kecap ABC Manis 225ml', 'cat_id' => $catBumbu, 'price' => 15000, 'batches' => [
                ['stock' => 30, 'buy' => 13000],
            ]],
            ['name' => 'Soffell Penolak Nyamuk', 'cat_id' => $catRt, 'price' => 7000, 'batches' => [
                ['stock' => 40, 'buy' => 5500],
            ]],
            ['name' => 'Cadbury Dairy Milk 62gr', 'cat_id' => $catJajanan, 'price' => 14000, 'batches' => [
                ['stock' => 30, 'buy' => 11000],
            ]],
            ['name' => 'Yakult 1 Pak (5 botol)', 'cat_id' => $catMinuman, 'price' => 13000, 'batches' => [
                ['stock' => 15, 'buy' => 11000],
            ]],
            ['name' => 'Minyak Goreng Tropical 2L', 'cat_id' => $catBumbu, 'price' => 34000, 'batches' => [
                ['stock' => 20, 'buy' => 30000],
            ]],
            ['name' => 'Tisu Gulung Tessa', 'cat_id' => $catRt, 'price' => 5000, 'batches' => [
                ['stock' => 40, 'buy' => 4000],
            ]],
            ['name' => 'Susu Ultra Strawberry 250ml', 'cat_id' => $catMinuman, 'price' => 6500, 'batches' => [
                ['stock' => 40, 'buy' => 5000],
            ]],
            ['name' => 'Attack Easy 700gr', 'cat_id' => $catRt, 'price' => 21000, 'batches' => [
                ['stock' => 25, 'buy' => 18000],
            ]],
            ['name' => 'Qtela Singkong Balado 60gr', 'cat_id' => $catJajanan, 'price' => 7000, 'batches' => [
                ['stock' => 30, 'buy' => 5500],
            ]],
            ['name' => 'Le Minerale 1500ml', 'cat_id' => $catMinuman, 'price' => 6000, 'batches' => [
                ['stock' => 30, 'buy' => 5000],
            ]],
            ['name' => 'Cimory Yoghurt Strawberry', 'cat_id' => $catMinuman, 'price' => 8000, 'batches' => [
                ['stock' => 25, 'buy' => 6500],
            ]],
        ];


        $products = [];
        $stocks = [];
        $currentProductId = 1;
        $productSellPrice = 0;

        foreach ($productsData as $product) {
            
            // 1. Siapkan data untuk tabel 'products'
            // Mengambil 'price' langsung dari array
            $products[] = [
                'id' => $currentProductId,
                'name' => $product['name'],
                'category_id' => $product['cat_id'],
                'sell_price' => $product['price'], // Menggunakan harga jual manual
                'image' => 'default.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $productSellPrice = $product['price'];

            // 2. Loop melalui setiap batch stok
            foreach ($product['batches'] as $batch) {
                
                // Masukkan data stok dengan harga beli yang sudah manual
                $stocks[] = [
                    'product_id' => $currentProductId,
                    'initial_stock' => $batch['stock'],
                    'remaining_stock' => $batch['stock'],
                    'buy_price' => $batch['buy'], // Menggunakan harga beli manual
                    'sell_price' => $productSellPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $currentProductId++;
        }

        // Masukkan semua data sekaligus
        DB::table('products')->insert($products);
        DB::table('stocks')->insert($stocks);
    }
}
