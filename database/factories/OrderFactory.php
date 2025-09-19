<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = $this->faker->numberBetween(1, 10);
        $totalPrice = $product->price * $quantity;

        return [
            'product_id' => $product->id,
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_REJECTED,
                Order::STATUS_DELIVERED
            ]),
            'order_code' => 'ORD-' . strtoupper($this->faker->bothify('####-???')),
            'count' => $quantity,
            'totalPrice' => $totalPrice,
            'tax_amount' => round($totalPrice * 0.1, 2),
            'discount_amount' => 0,
            'reject_reason' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_PENDING,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_CONFIRMED,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_REJECTED,
            'reject_reason' => $this->faker->sentence(),
        ]);
    }

    public function withDiscount(float $discountPercent = 10): static
    {
        return $this->state(function (array $attributes) use ($discountPercent) {
            $discountAmount = round($attributes['totalPrice'] * ($discountPercent / 100), 2);
            return [
                'discount_amount' => $discountAmount,
            ];
        });
    }
}