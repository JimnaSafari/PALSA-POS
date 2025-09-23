<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryService = new InventoryService();
    }

    public function test_check_availability_returns_true_for_sufficient_stock()
    {
        $product = Product::factory()->create(['count' => 100]);

        $result = $this->inventoryService->checkAvailability($product->id, 50);

        $this->assertTrue($result);
    }

    public function test_check_availability_returns_false_for_insufficient_stock()
    {
        $product = Product::factory()->create(['count' => 10]);
        
        // Create orders that reduce available stock
        Order::factory()->create([
            'product_id' => $product->id,
            'count' => 8,
            'status' => Order::STATUS_CONFIRMED
        ]);

        $result = $this->inventoryService->checkAvailability($product->id, 5);

        $this->assertFalse($result);
    }

    public function test_get_low_stock_products()
    {
        // Product with low stock
        $lowStockProduct = Product::factory()->create(['count' => 5]);
        
        // Product with sufficient stock
        $normalProduct = Product::factory()->create(['count' => 100]);
        
        // Product with orders reducing stock to low level
        $reducedStockProduct = Product::factory()->create(['count' => 20]);
        Order::factory()->create([
            'product_id' => $reducedStockProduct->id,
            'count' => 15,
            'status' => Order::STATUS_CONFIRMED
        ]);

        $lowStockProducts = $this->inventoryService->getLowStockProducts(10);

        $this->assertCount(2, $lowStockProducts);
        $this->assertTrue($lowStockProducts->contains($lowStockProduct));
        $this->assertTrue($lowStockProducts->contains($reducedStockProduct));
        $this->assertFalse($lowStockProducts->contains($normalProduct));
    }

    public function test_validate_order_quantities()
    {
        $product1 = Product::factory()->create(['count' => 10]);
        $product2 = Product::factory()->create(['count' => 5]);

        $orderItems = [
            ['product_id' => $product1->id, 'quantity' => 5], // Valid
            ['product_id' => $product2->id, 'quantity' => 10], // Invalid - exceeds stock
        ];

        $errors = $this->inventoryService->validateOrderQuantities($orderItems);

        $this->assertCount(1, $errors);
        $this->assertEquals($product2->id, $errors[0]['product_id']);
    }

    public function test_stock_report_calculation()
    {
        Product::factory()->create(['count' => 100, 'price' => 50, 'purchase_price' => 30]);
        Product::factory()->create(['count' => 50, 'price' => 20, 'purchase_price' => 10]);

        $report = $this->inventoryService->getStockReport();

        $this->assertEquals(2, $report['total_products']);
        $this->assertEquals(3500, $report['total_stock_value']); // (100*30) + (50*10)
        $this->assertEquals(6000, $report['total_retail_value']); // (100*50) + (50*20)
        $this->assertEquals(2500, $report['potential_profit']); // 6000 - 3500
    }
}