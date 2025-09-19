<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user if not exists
        if (!User::where('email', 'superadmin@gmail.com')->exists()) {
            User::create([
                'name' => 'Super Administrator',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_SUPERADMIN,
                'phone' => '+1234567890',
                'address' => 'Admin Office'
            ]);
        }

        // Create default admin user
        if (!User::where('email', 'admin@palsapos.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@palsapos.com',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'phone' => '+1234567891',
                'address' => 'Admin Office'
            ]);
        }

        // Create sample customer
        if (!User::where('email', 'customer@example.com')->exists()) {
            User::create([
                'name' => 'Sample Customer',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'phone' => '+1234567892',
                'address' => '123 Customer Street'
            ]);
        }

        // Create default categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories', 'is_active' => true],
            ['name' => 'Clothing', 'description' => 'Apparel and fashion items', 'is_active' => true],
            ['name' => 'Books', 'description' => 'Books and educational materials', 'is_active' => true],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies', 'is_active' => true],
            ['name' => 'Sports', 'description' => 'Sports equipment and accessories', 'is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        // Create sample products
        $electronicsCategory = Category::where('name', 'Electronics')->first();
        $clothingCategory = Category::where('name', 'Clothing')->first();

        $products = [
            [
                'name' => 'Samsung Galaxy A54',
                'price' => 45000.00, // KES 45,000
                'purchase_price' => 38000.00, // KES 38,000
                'category_id' => $electronicsCategory->id,
                'description' => 'Latest Samsung smartphone with advanced camera features',
                'count' => 50,
                'min_stock_level' => 10,
                'sku' => 'PHONE-001',
                'is_active' => true
            ],
            [
                'name' => 'HP Pavilion Laptop',
                'price' => 85000.00, // KES 85,000
                'purchase_price' => 70000.00, // KES 70,000
                'category_id' => $electronicsCategory->id,
                'description' => 'High-performance laptop for work and entertainment',
                'count' => 25,
                'min_stock_level' => 5,
                'sku' => 'LAPTOP-001',
                'is_active' => true
            ],
            [
                'name' => 'Cotton T-Shirt',
                'price' => 1500.00, // KES 1,500
                'purchase_price' => 800.00, // KES 800
                'category_id' => $clothingCategory->id,
                'description' => 'Comfortable 100% cotton t-shirt, various colors',
                'count' => 100,
                'min_stock_level' => 20,
                'sku' => 'TSHIRT-001',
                'is_active' => true
            ],
            [
                'name' => 'Denim Jeans',
                'price' => 3500.00, // KES 3,500
                'purchase_price' => 2200.00, // KES 2,200
                'category_id' => $clothingCategory->id,
                'description' => 'Premium quality denim jeans, multiple sizes',
                'count' => 75,
                'min_stock_level' => 15,
                'sku' => 'JEANS-001',
                'is_active' => true
            ],
            [
                'name' => 'Maize Flour (2kg)',
                'price' => 180.00, // KES 180
                'purchase_price' => 150.00, // KES 150
                'category_id' => Category::where('name', 'Home & Garden')->first()->id,
                'description' => 'Premium maize flour, 2kg pack',
                'count' => 200,
                'min_stock_level' => 50,
                'sku' => 'FLOUR-001',
                'is_active' => true
            ],
            [
                'name' => 'Cooking Oil (1L)',
                'price' => 320.00, // KES 320
                'purchase_price' => 280.00, // KES 280
                'category_id' => Category::where('name', 'Home & Garden')->first()->id,
                'description' => 'Pure sunflower cooking oil, 1 liter',
                'count' => 150,
                'min_stock_level' => 30,
                'sku' => 'OIL-001',
                'is_active' => true
            ]
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        // Create Kenyan payment methods
        $paymentMethods = [
            [
                'type' => 'M-Pesa', 
                'account_number' => '522522', 
                'account_name' => 'Palsa POS Paybill',
                'description' => 'Safaricom M-Pesa mobile money'
            ],
            [
                'type' => 'Airtel Money', 
                'account_number' => '100100', 
                'account_name' => 'Palsa POS Airtel',
                'description' => 'Airtel Money mobile payment'
            ],
            [
                'type' => 'T-Kash', 
                'account_number' => '460460', 
                'account_name' => 'Palsa POS T-Kash',
                'description' => 'Telkom T-Kash mobile money'
            ],
            [
                'type' => 'Equitel', 
                'account_number' => '247247', 
                'account_name' => 'Palsa POS Equitel',
                'description' => 'Equity Bank Equitel payment'
            ],
            [
                'type' => 'KCB Bank', 
                'account_number' => '1234567890', 
                'account_name' => 'Palsa POS Business Account',
                'description' => 'Kenya Commercial Bank transfer'
            ],
            [
                'type' => 'Equity Bank', 
                'account_number' => '0987654321', 
                'account_name' => 'Palsa POS Business Account',
                'description' => 'Equity Bank transfer'
            ],
            [
                'type' => 'Co-op Bank', 
                'account_number' => '01129123456789', 
                'account_name' => 'Palsa POS Business Account',
                'description' => 'Co-operative Bank transfer'
            ],
            [
                'type' => 'Cash Payment', 
                'account_number' => 'CASH-KE', 
                'account_name' => 'Cash on Delivery/Collection',
                'description' => 'Pay cash when collecting order'
            ],
            [
                'type' => 'Visa/Mastercard', 
                'account_number' => 'CARD-KE', 
                'account_name' => 'Card Payment Gateway',
                'description' => 'Debit/Credit card payment'
            ]
        ];

        foreach ($paymentMethods as $paymentData) {
            Payment::firstOrCreate(
                ['type' => $paymentData['type']],
                $paymentData
            );
        }

        $this->command->info('Production seeder completed successfully!');
        $this->command->info('Default users created:');
        $this->command->info('- superadmin@gmail.com (password: admin123)');
        $this->command->info('- admin@palsapos.com (password: admin123)');
        $this->command->info('- customer@example.com (password: password)');
    }
}