<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ItemCondition;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemCondition>
 */
class ItemConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ItemCondition::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
