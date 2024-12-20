<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;
use App\Models\ItemCondition;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'condition_id' => ItemCondition::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->text(255),
            'price' => $this->faker->numberBetween(1000, 9999999),
            'image_path' => $this->faker->imageUrl(),
            'brand' => $this->faker->company(),
        ];
    }
}
