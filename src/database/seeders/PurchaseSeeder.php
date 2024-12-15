<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('purchases')->insert([
            [
                'user_id' => 1,
                'item_id' => 8,
                'payment_method' => 1,
                'postal_code' => '123-4567',
                'address' => '愛知県名古屋市1-1-1',
                'building_name' => '某ビル',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'item_id' => 3,
                'payment_method' => 2,
                'postal_code' => '987-6543',
                'address' => '大阪府大阪市2-2-2',
                'building_name' => '某マンション',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
