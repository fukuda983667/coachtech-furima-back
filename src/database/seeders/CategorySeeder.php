<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // カテゴリのデータを作成
        $categories = [
            ['name' => 'ファッション'],
            ['name' => '家電'],
            ['name' => 'インテリア'],
            ['name' => 'レディース'],
            ['name' => 'メンズ'],
            ['name' => 'コスメ'],
            ['name' => '本'],
            ['name' => 'ゲーム'],
            ['name' => 'スポーツ'],
            ['name' => 'キッチン'],
            ['name' => 'ハンドメイド'],
            ['name' => 'アクセサリー'],
            ['name' => 'おもちゃ'],
            ['name' => 'ベビー·キッズ'],
            ['name' => '時計'],
            ['name' => '電子機器'],
            ['name' => '食品'],
            ['name' => 'シューズ'],
            ['name' => 'バッグ'],
            ['name' => '日用品'],
        ];

        // 各カテゴリをデータベースに挿入
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
