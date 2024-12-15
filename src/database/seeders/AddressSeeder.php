<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('addresses')->insert([
            [
                'user_id' => 1,
                'postal_code' => '123-4567',
                'address' => '東京都',
                'building_name' => '某オフィスビル',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'postal_code' => '111-1111',
                'address' => '北海道',
                'building_name' => '某倉庫',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'postal_code' => '987-6543',
                'address' => '愛知県名古屋市',
                'building_name' => '某商社',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 追加のアドレスデータをここに記述
        ]);
    }
}
