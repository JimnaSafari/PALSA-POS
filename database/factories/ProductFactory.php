<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $purchasePrice = $this->faker->randomFloat(2, 10, 500);
        $sellingPrice = $purchasePrice * $this->faker->randomFloat(2, 1.2, 3.0); // 20% to 200% markup

        return [
            'name' => $this->faker->words(3, true),
            'price' => round($sellingPrice, 2),
            'purchase_price' => round($purchasePrice, 2),
            'category_id' => Category::factory(),
            'description' => $this->faker->paragraph(),
            'count' => $this->faker->numberBetween(0, 1000),
            'image' => $this->faker->imageUrl(640, 480, 'products'),
            'sku' => strtoupper($this->faker->bothify('SKU-####-???')),
            'barcode' => $this->faker->ean13(),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'count' => $this->faker->numberBetween(0, 10),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'count' => 0,
        ]);
    }

    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, 500, 2000),
            'purchase_price' => $this->faker->randomFloat(2, 300, 1200),
        ]);
    }
}