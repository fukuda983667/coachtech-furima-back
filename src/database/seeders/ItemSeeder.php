<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'name' => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'user_id' => 1, // ユーザーIDは適宜変更
                'image_path' => "1.jpg",
                'condition_id' => 1,
                'brand' => "テスト太郎商会",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'user_id' => 1,
                'image_path' => "2.jpg",
                'condition_id' => 2,
                'brand' => "テスト太郎商会",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'user_id' => 1,
                'image_path' => "3.jpg",
                'condition_id' => 3,
                'brand' => "テスト太郎商会",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'user_id' => 1,
                'image_path' => "4.jpg",
                'condition_id' => 4,
                'brand' => "テスト太郎商会",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ノートPC',
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'user_id' => 1,
                'image_path' => "5.jpg",
                'condition_id' => 1,
                'brand' => "テスト太郎商会",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'user_id' => 2,
                'image_path' => "6.jpg",
                'condition_id' => 2,
                'brand' => "テスト花子カンパニー",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'user_id' => 2,
                'image_path' => "7.jpg",
                'condition_id' => 3,
                'brand' => "テスト花子カンパニー",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'user_id' => 2,
                'image_path' => "8.jpg",
                'condition_id' => 4,
                'brand' => "テスト花子カンパニー",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'user_id' => 2,
                'image_path' => "9.jpg",
                'condition_id' => 1,
                'brand' => "テスト花子カンパニー",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'user_id' => 2,
                'image_path' => "10.jpg",
                'condition_id' => 2,
                'brand' => "テスト花子カンパニー",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 追加のデータをここに記述
        ]);
    }
}
