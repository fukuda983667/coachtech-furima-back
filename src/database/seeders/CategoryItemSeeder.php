<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class CategoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // item_id に基づいて特定のカテゴリを割り当て
        $itemsWithCategories = [
            1 => [5, 15], // 腕時計にカテゴリ5と15を割り当て
            2 => [16],
            3 => [17],
            4 => [18],
            5 => [16],
            6 => [16],
            7 => [19],
            8 => [20],
            9 => [20],
            10 => [6],
        ];

        foreach ($itemsWithCategories as $itemId => $categoryIds) {
            foreach ($categoryIds as $categoryId) {
                DB::table('category_item')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
