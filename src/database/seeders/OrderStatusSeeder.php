<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => '注文確定', ],
            ['name' => '支払済', ],
            ['name' => '未支払', ],
            ['name' => '発送中', ],
            ['name' => '発送済', ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::create($status);
        }
    }
}
