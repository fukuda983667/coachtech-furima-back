<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item = Item::create([
            'name' => '腕時計',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'user_id' => 1, // ユーザーIDは適宜変更
            'image_path' => "1.jpg",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // カテゴリ5と15を割り当て
        $item->categories()->attach([5, 15]);

        DB::table('items')->insert([
            [
                'name' => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'category_id' => 16,
                'user_id' => 1,
                'image_path' => "2.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'category_id' => 17,
                'user_id' => 1,
                'image_path' => "3.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'category_id' => 18,
                'user_id' => 1,
                'image_path' => "4.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ノートPC',
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'category_id' => 16,
                'user_id' => 1,
                'image_path' => "5.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'category_id' => 16,
                'user_id' => 2,
                'image_path' => "6.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'category_id' => 19,
                'user_id' => 2,
                'image_path' => "7.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'category_id' => 20,
                'user_id' => 2,
                'image_path' => "8.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'category_id' => 20,
                'user_id' => 2,
                'image_path' => "9.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'category_id' => 6,
                'user_id' => 2,
                'image_path' => "10.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 追加のデータをここに記述
        ]);
    }
}
