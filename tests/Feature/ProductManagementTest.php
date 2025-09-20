<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_product_list()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $products = Product::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('productList'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.product.list');
        $response->assertViewHas('products');
    }

    public function test_admin_can_create_product()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::factory()->create();
        
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'purchaseprice' => 50.00,
            'categoryName' => $category->id,
            'count' => 100,
            'description' => 'Test product description',
            'image' => UploadedFile::fake()->image('product.jpg', 800, 600)
        ];

        $response = $this->actingAs($admin)->post(route('productCreate'), $productData);

        $response->assertRedirect(route('productList'));
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'purchase_price' => 50.00
        ]);
    }

    public function test_regular_user_cannot_access_admin_product_routes()
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('productList'));

        $response->assertStatus(403);
    }

    public function test_product_validation_works()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        
        $invalidData = [
            'name' => '', // Required field empty
            'price' => -10, // Negative price
            'purchaseprice' => 200, // Higher than selling price
            'categoryName' => 999, // Non-existent category
            'count' => -5, // Negative count
            'description' => ''
        ];

        $response = $this->actingAs($admin)->post(route('productCreate'), $invalidData);

        $response->assertSessionHasErrors([
            'name', 'price', 'purchaseprice', 'categoryName', 'count', 'description', 'image'
        ]);
    }

    public function test_product_stock_calculation()
    {
        $product = Product::factory()->create(['count' => 100]);
        
        // Create some confirmed orders
        Order::factory()->create([
            'product_id' => $product->id,
            'count' => 20,
            'status' => Order::STATUS_CONFIRMED
        ]);
        
        Order::factory()->create([
            'product_id' => $product->id,
            'count' => 15,
            'status' => Order::STATUS_CONFIRMED
        ]);

        $this->assertEquals(65, $product->available_stock);
    }

    public function test_product_search_functionality()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        
        Product::factory()->create(['name' => 'iPhone 15']);
        Product::factory()->create(['name' => 'Samsung Galaxy']);
        Product::factory()->create(['name' => 'iPad Pro']);

        $response = $this->actingAs($admin)->get(route('productList', ['searchKey' => 'iPhone']));

        $response->assertStatus(200);
        $response->assertSee('iPhone 15');
        $response->assertDontSee('Samsung Galaxy');
    }
}