<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('comments')->insert([
            [
                'user_id' => 1,
                'item_id' => 7,
                'comment' => 'この商品はとても素晴らしいです！',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'item_id' => 7,
                'comment' => 'この商品は値下げ可能ですか？',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'item_id' => 7,
                'comment' => '3000円まで値下げできますか？',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'item_id' => 7,
                'comment' => '汚れや傷の箇所、程度を詳細に教えてください',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'item_id' => 1,
                'comment' => '何年使用していましたか？',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
