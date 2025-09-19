<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function checkAvailability(int $productId, int $quantity): bool
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return false;
        }

        return $product->available_stock >= $quantity;
    }

    public function reserveStock(int $productId, int $quantity): bool
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $product = Product::lockForUpdate()->find($productId);
            
            if (!$product || $product->available_stock < $quantity) {
                return false;
            }

            // Stock is managed through orders, so we don't need to update product count here
            // The available_stock attribute calculates this automatically
            
            Log::info("Stock reserved", [
                'product_id' => $productId,
                'quantity' => $quantity,
                'remaining_stock' => $product->available_stock - $quantity
            ]);

            return true;
        });
    }

    public function releaseStock(int $productId, int $quantity): void
    {
        DB::transaction(function () use ($productId, $quantity) {
            $product = Product::lockForUpdate()->find($productId);
            
            if ($product) {
                Log::info("Stock released", [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'current_stock' => $product->available_stock
                ]);
            }
        });
    }

    public function updateStock(int $productId, int $newStock): bool
    {
        return DB::transaction(function () use ($productId, $newStock) {
            $product = Product::lockForUpdate()->find($productId);
            
            if (!$product) {
                return false;
            }

            $oldStock = $product->count;
            $product->update(['count' => $newStock]);
            
            Log::info("Stock updated", [
                'product_id' => $productId,
                'old_stock' => $oldStock,
                'new_stock' => $newStock
            ]);

            return true;
        });
    }

    public function getLowStockProducts(int $threshold = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Product::whereRaw('count - IFNULL((
            SELECT SUM(orders.count) 
            FROM orders 
            WHERE orders.product_id = products.id 
            AND orders.status = ?
        ), 0) <= ?', [Order::STATUS_CONFIRMED, $threshold])
        ->with('category')
        ->get();
    }

    public function getStockReport(): array
    {
        $totalProducts = Product::count();
        $lowStockProducts = $this->getLowStockProducts()->count();
        $outOfStockProducts = $this->getLowStockProducts(0)->count();
        
        $totalStockValue = Product::sum(DB::raw('count * purchase_price'));
        $totalRetailValue = Product::sum(DB::raw('count * price'));

        return [
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'total_stock_value' => $totalStockValue,
            'total_retail_value' => $totalRetailValue,
            'potential_profit' => $totalRetailValue - $totalStockValue
        ];
    }

    public function validateOrderQuantities(array $orderItems): array
    {
        $errors = [];
        
        foreach ($orderItems as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];
            
            if (!$this->checkAvailability($productId, $quantity)) {
                $product = Product::find($productId);
                $errors[] = [
                    'product_id' => $productId,
                    'product_name' => $product->name ?? 'Unknown',
                    'requested_quantity' => $quantity,
                    'available_quantity' => $product->available_stock ?? 0,
                    'message' => "Insufficient stock for {$product->name}. Available: {$product->available_stock}, Requested: {$quantity}"
                ];
            }
        }

        return $errors;
    }
}