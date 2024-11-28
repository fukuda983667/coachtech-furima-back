<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemCondition;

class ItemConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [
            ['name' => '良好', ],
            ['name' => '目立った傷や汚れなし', ],
            ['name' => 'やや傷や汚れあり', ],
            ['name' => '状態が悪い', ],
        ];

        foreach ($conditions as $condition) {
            ItemCondition::create($condition);
        }
    }
}
