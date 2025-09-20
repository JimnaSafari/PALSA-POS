<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Electronics',
                'Clothing',
                'Books',
                'Home & Garden',
                'Sports',
                'Toys',
                'Beauty',
                'Automotive',
                'Food & Beverages',
                'Health'
            ]),
            'description' => $this->faker->sentence(),
        ];
    }
}