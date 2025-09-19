<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendLowStockAlerts extends Command
{
    protected $signature = 'pos:low-stock-alerts';
    protected $description = 'Send alerts for products with low stock levels';

    public function handle(NotificationService $notificationService): int
    {
        $this->info('Checking for low stock products...');

        $lowStockProducts = Product::whereRaw('count <= min_stock_level')
            ->with('category')
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low stock products found.');
            return self::SUCCESS;
        }

        $this->warn("Found {$lowStockProducts->count()} products with low stock:");

        $productsData = $lowStockProducts->map(function ($product) {
            $this->line("- {$product->name} (Stock: {$product->count}, Min: {$product->min_stock_level})");
            
            return [
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $product->count,
                'min_level' => $product->min_stock_level,
                'category' => $product->category->name ?? 'N/A'
            ];
        })->toArray();

        $notificationService->sendLowStockAlert($productsData);

        $this->info('Low stock alerts sent successfully.');
        return self::SUCCESS;
    }
}