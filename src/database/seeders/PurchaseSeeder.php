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
                'address_id' => 1,
                'payment_method' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'item_id' => 3,
                'address_id' => 3,
                'payment_method' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
