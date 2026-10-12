<?php

namespace Database\Factories;

use App\Models\Stand;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Stand>
 */
class StandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Stand '.fake()->company();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(10, 999),
            'stand_number' => 'Stand '.fake()->numberBetween(1, 20),
            'phone' => fake()->phoneNumber(),
            'description' => fake()->sentence(),
            'image' => 'assets/images/food-menu-1.png',
            'is_open' => true,
            'opening_time' => '06:30:00',
            'closing_time' => '16:00:00',
        ];
    }
}
