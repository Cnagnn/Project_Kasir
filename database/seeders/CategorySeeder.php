<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => "Makanan Instan", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Minuman", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Bumbu Dapur", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Kebutuhan Rumah Tangga", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Perlengkapan Mandi", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Jajanan & Makanan Ringan", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Kopi & Teh", 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}
