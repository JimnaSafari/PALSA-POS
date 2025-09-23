<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public function getSalesReport(Carbon $startDate, Carbon $endDate): array
    {
        $orders = Order::confirmed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['product.category', 'user'])
            ->get();

        $totalSales = $orders->sum('totalPrice');
        $totalOrders = $orders->count();
        $totalTax = $orders->sum('tax_amount');
        $totalDiscount = $orders->sum('discount_amount');

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'summary' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'total_tax' => $totalTax,
                'total_discount' => $totalDiscount,
                'net_sales' => $totalSales - $totalDiscount,
                'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0
            ],
            'daily_breakdown' => $this->getDailyBreakdown($orders, $startDate, $endDate),
            'category_breakdown' => $this->getCategoryBreakdown($orders),
            'top_products' => $this->getTopProducts($orders),
            'top_customers' => $this->getTopCustomers($orders)
        ];
    }

    public function getInventoryReport(): array
    {
        $products = Product::with('category')->get();
        
        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->count * $product->purchase_price;
        });
        $totalRetailValue = $products->sum(function ($product) {
            return $product->count * $product->price;
        });

        $lowStockProducts = $products->filter(function ($product) {
            return $product->count <= ($product->min_stock_level ?? 10);
        });

        $outOfStockProducts = $products->where('count', 0);

        return [
            'summary' => [
                'total_products' => $totalProducts,
                'total_stock_value' => $totalStockValue,
                'total_retail_value' => $totalRetailValue,
                'potential_profit' => $totalRetailValue - $totalStockValue,
                'low_stock_count' => $lowStockProducts->count(),
                'out_of_stock_count' => $outOfStockProducts->count()
            ],
            'low_stock_products' => $lowStockProducts->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $product->count,
                    'min_level' => $product->min_stock_level ?? 10,
                    'category' => $product->category->name ?? 'N/A'
                ];
            })->values(),
            'category_stock' => $this->getCategoryStockBreakdown($products),
            'stock_movement' => $this->getStockMovement()
        ];
    }

    public function getCustomerReport(): array
    {
        $customers = User::where('role', User::ROLE_USER)
            ->withCount('orders')
            ->with(['orders' => function ($query) {
                $query->confirmed();
            }])
            ->get();

        $totalCustomers = $customers->count();
        $activeCustomers = $customers->filter(function ($customer) {
            return $customer->orders->where('created_at', '>=', Carbon::now()->subDays(30))->count() > 0;
        })->count();

        return [
            'summary' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'new_customers_this_month' => User::where('role', User::ROLE_USER)
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->count()
            ],
            'top_customers' => $customers->map(function ($customer) {
                $totalSpent = $customer->orders->sum('totalPrice');
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'total_orders' => $customer->orders_count,
                    'total_spent' => $totalSpent,
                    'last_order' => $customer->orders->max('created_at')
                ];
            })->sortByDesc('total_spent')->take(10)->values(),
            'customer_acquisition' => $this->getCustomerAcquisition()
        ];
    }

    public function getProfitLossReport(Carbon $startDate, Carbon $endDate): array
    {
        $orders = Order::confirmed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('product')
            ->get();

        $revenue = $orders->sum('totalPrice');
        $cogs = $orders->sum(function ($order) {
            return $order->product->purchase_price * $order->count;
        });
        $grossProfit = $revenue - $cogs;
        $taxes = $orders->sum('tax_amount');
        $discounts = $orders->sum('discount_amount');

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'revenue' => [
                'gross_sales' => $revenue,
                'discounts' => $discounts,
                'net_sales' => $revenue - $discounts
            ],
            'costs' => [
                'cost_of_goods_sold' => $cogs,
                'taxes' => $taxes
            ],
            'profit' => [
                'gross_profit' => $grossProfit,
                'gross_margin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0,
                'net_profit' => $grossProfit - $taxes
            ],
            'product_profitability' => $this->getProductProfitability($orders)
        ];
    }

    private function getDailyBreakdown($orders, Carbon $startDate, Carbon $endDate): array
    {
        $dailyData = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dayOrders = $orders->filter(function ($order) use ($current) {
                return $order->created_at->isSameDay($current);
            });

            $dailyData[] = [
                'date' => $current->format('Y-m-d'),
                'sales' => $dayOrders->sum('totalPrice'),
                'orders' => $dayOrders->count(),
                'items_sold' => $dayOrders->sum('count')
            ];

            $current->addDay();
        }

        return $dailyData;
    }

    private function getCategoryBreakdown($orders): array
    {
        return $orders->groupBy('product.category.name')
            ->map(function ($categoryOrders, $categoryName) {
                return [
                    'category' => $categoryName ?? 'Uncategorized',
                    'sales' => $categoryOrders->sum('totalPrice'),
                    'orders' => $categoryOrders->count(),
                    'items_sold' => $categoryOrders->sum('count')
                ];
            })
            ->sortByDesc('sales')
            ->values()
            ->toArray();
    }

    private function getTopProducts($orders): array
    {
        return $orders->groupBy('product.name')
            ->map(function ($productOrders, $productName) {
                $product = $productOrders->first()->product;
                return [
                    'product' => $productName,
                    'sku' => $product->sku ?? 'N/A',
                    'quantity_sold' => $productOrders->sum('count'),
                    'revenue' => $productOrders->sum('totalPrice'),
                    'orders' => $productOrders->count()
                ];
            })
            ->sortByDesc('revenue')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function getTopCustomers($orders): array
    {
        return $orders->groupBy('user.name')
            ->map(function ($customerOrders, $customerName) {
                return [
                    'customer' => $customerName,
                    'total_spent' => $customerOrders->sum('totalPrice'),
                    'orders' => $customerOrders->count(),
                    'items_purchased' => $customerOrders->sum('count')
                ];
            })
            ->sortByDesc('total_spent')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function getCategoryStockBreakdown($products): array
    {
        return $products->groupBy('category.name')
            ->map(function ($categoryProducts, $categoryName) {
                return [
                    'category' => $categoryName ?? 'Uncategorized',
                    'products_count' => $categoryProducts->count(),
                    'total_stock' => $categoryProducts->sum('count'),
                    'stock_value' => $categoryProducts->sum(function ($product) {
                        return $product->count * $product->purchase_price;
                    })
                ];
            })
            ->values()
            ->toArray();
    }

    private function getStockMovement(): array
    {
        // This would track stock movements over time
        // For now, we'll return recent sales as stock movement
        return Order::confirmed()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($order) {
                return [
                    'date' => $order->created_at->format('Y-m-d H:i'),
                    'product' => $order->product->name,
                    'type' => 'sale',
                    'quantity' => -$order->count, // Negative for sales
                    'reference' => $order->order_code
                ];
            })
            ->toArray();
    }

    private function getCustomerAcquisition(): array
    {
        $last12Months = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = User::where('role', User::ROLE_USER)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
                
            $last12Months[] = [
                'month' => $month->format('M Y'),
                'new_customers' => $count
            ];
        }

        return $last12Months;
    }

    private function getProductProfitability($orders): array
    {
        return $orders->groupBy('product.name')
            ->map(function ($productOrders, $productName) {
                $product = $productOrders->first()->product;
                $revenue = $productOrders->sum('totalPrice');
                $cost = $productOrders->sum(function ($order) use ($product) {
                    return $product->purchase_price * $order->count;
                });
                $profit = $revenue - $cost;
                
                return [
                    'product' => $productName,
                    'revenue' => $revenue,
                    'cost' => $cost,
                    'profit' => $profit,
                    'margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0
                ];
            })
            ->sortByDesc('profit')
            ->take(10)
            ->values()
            ->toArray();
    }
}