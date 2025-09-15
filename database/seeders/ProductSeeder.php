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
        DB::table('products')->insert([
            'name' => "Indomie Goreng",
            'category_id' => 1,
            'image' => 'image.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_stock_batches')->insert([
            'product_id' => 1,
            'initial_stock' => 10,
            'remaining_stock' => 10,
            'buy_price' => 2000,
            'sell_price' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_stock_batches')->insert([
            'product_id' => 1,
            'initial_stock' => 10,
            'remaining_stock' => 10,
            'buy_price' => 2000,
            'sell_price' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'name' => "Teh Botol",
            'category_id' => 2,
            'image' => 'image.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_stock_batches')->insert([
            'product_id' => 2,
            'initial_stock' => 10,
            'remaining_stock' => 10,
            'buy_price' => 3000,
            'sell_price' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
