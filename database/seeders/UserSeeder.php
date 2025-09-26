<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => "Yoga Pratama Putra Rizqulloh",
            'email' => "yogapratama@gmail.com",
            'phone' => '08123456789',
            'password' => bcrypt('230304'),
            'roles_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => "Arya Cahya Fauzan",
            'email' => "aryafauzan191003@gmail.com",
            'phone' => '082264698950',
            'password' => bcrypt('191003'),
            'roles_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
