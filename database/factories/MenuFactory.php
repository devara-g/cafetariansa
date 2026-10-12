<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Stand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $price = fake()->randomElement([5000, 8000, 10000, 12000, 15000, 18000]);

        return [
            'stand_id' => Stand::factory(),
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(),
            'price' => $price,
            'cost_price' => (int) ($price * 0.7),
            'image' => 'assets/images/food-menu-1.png',
            'stock' => fake()->numberBetween(10, 100),
            'is_available' => true,
            'is_popular' => fake()->boolean(40),
            'is_recommended' => fake()->boolean(30),
            'preparation_time_minutes' => fake()->numberBetween(3, 15),
        ];
    }
}
