<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // ユーザーに関連づけ
            'item_id' => Item::factory(), // アイテムに関連づけ
            'address_id' => Address::factory(), // 住所に関連づけ
            'payment_method' => $this->faker->numberBetween(1, 2), // 支払い方法（1〜3のランダムな数字）
        ];
    }
}
