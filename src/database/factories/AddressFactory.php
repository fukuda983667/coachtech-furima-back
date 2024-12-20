<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Address;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'postal_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'building_name' => $this->faker->word,
            'is_default' => $this->faker->boolean,
        ];
    }
}
