<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getAdminDashboardData(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        return [
            'sales' => $this->getSalesData($today, $thisMonth),
            'orders' => $this->getOrdersData($today),
            'products' => $this->getProductsData(),
            'customers' => $this->getCustomersData($thisMonth),
            'charts' => $this->getChartsData(),
            'alerts' => $this->getSystemAlerts()
        ];
    }

    private function getSalesData(Carbon $today, Carbon $thisMonth): array
    {
        $todaySales = Order::confirmed()
            ->whereDate('created_at', $today)
            ->sum('totalPrice');

        $monthSales = Order::confirmed()
            ->where('created_at', '>=', $thisMonth)
            ->sum('totalPrice');

        $lastMonthSales = Order::confirmed()
            ->whereBetween('created_at', [
                $thisMonth->copy()->subMonth(),
                $thisMonth->copy()->subSecond()
            ])
            ->sum('totalPrice');

        $monthlyGrowth = $lastMonthSales > 0 
            ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 
            : 0;

        return [
            'today' => $todaySales,
            'this_month' => $monthSales,
            'last_month' => $lastMonthSales,
            'monthly_growth' => round($monthlyGrowth, 2),
            'average_order_value' => $this->getAverageOrderValue()
        ];
    }

    private function getOrdersData(Carbon $today): array
    {
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $pendingOrders = Order::pending()->count();
        $confirmedOrders = Order::confirmed()->whereDate('created_at', $today)->count();
        
        return [
            'today_total' => $todayOrders,
            'pending' => $pendingOrders,
            'confirmed_today' => $confirmedOrders,
            'recent_orders' => $this->getRecentOrders()
        ];
    }

    private function getProductsData(): array
    {
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereRaw('count <= min_stock_level')->count();
        $outOfStockProducts = Product::where('count', 0)->count();
        
        return [
            'total' => $totalProducts,
            'low_stock' => $lowStockProducts,
            'out_of_stock' => $outOfStockProducts,
            'top_selling' => $this->getTopSellingProducts(),
            'categories_count' => Category::count()
        ];
    }

    private function getCustomersData(Carbon $thisMonth): array
    {
        $totalCustomers = User::where('role', User::ROLE_USER)->count();
        $newCustomers = User::where('role', User::ROLE_USER)
            ->where('created_at', '>=', $thisMonth)
            ->count();

        return [
            'total' => $totalCustomers,
            'new_this_month' => $newCustomers,
            'active_customers' => $this->getActiveCustomersCount()
        ];
    }

    private function getChartsData(): array
    {
        return [
            'daily_sales' => $this->getDailySalesChart(),
            'category_sales' => $this->getCategorySalesChart(),
            'order_status_distribution' => $this->getOrderStatusChart()
        ];
    }

    private function getDailySalesChart(): array
    {
        $last7Days = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sales = Order::confirmed()
                ->whereDate('created_at', $date)
                ->sum('totalPrice');
                
            $last7Days->push([
                'date' => $date->format('M d'),
                'sales' => (float) $sales
            ]);
        }

        return $last7Days->toArray();
    }

    private function getCategorySalesChart(): array
    {
        return DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', Order::STATUS_CONFIRMED)
            ->select('categories.name', DB::raw('SUM(orders.totalPrice) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->name,
                    'sales' => (float) $item->total_sales
                ];
            })
            ->toArray();
    }

    private function getOrderStatusChart(): array
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $this->getStatusName($item->status),
                    'count' => $item->count
                ];
            })
            ->toArray();
    }

    private function getRecentOrders(): array
    {
        return Order::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer' => $order->user->name,
                    'product' => $order->product->name,
                    'amount' => $order->totalPrice,
                    'status' => $order->status_text,
                    'created_at' => $order->created_at->diffForHumans()
                ];
            })
            ->toArray();
    }

    private function getTopSellingProducts(): array
    {
        return DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.status', Order::STATUS_CONFIRMED)
            ->select('products.name', DB::raw('SUM(orders.count) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'sold' => $item->total_sold
                ];
            })
            ->toArray();
    }

    private function getAverageOrderValue(): float
    {
        $avgValue = Order::confirmed()->avg('totalPrice');
        return round($avgValue ?? 0, 2);
    }

    private function getActiveCustomersCount(): int
    {
        return User::where('role', User::ROLE_USER)
            ->whereHas('orders', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->count();
    }

    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Low stock alerts
        $lowStockCount = Product::whereRaw('count <= min_stock_level')->count();
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$lowStockCount} products are running low on stock",
                'action_url' => route('productList', ['filter' => 'low_stock'])
            ];
        }

        // Pending orders alert
        $pendingCount = Order::pending()->count();
        if ($pendingCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingCount} orders are pending approval",
                'action_url' => route('orderListPage')
            ];
        }

        return $alerts;
    }

    private function getStatusName(int $status): string
    {
        return match($status) {
            Order::STATUS_PENDING => 'Pending',
            Order::STATUS_CONFIRMED => 'Confirmed',
            Order::STATUS_REJECTED => 'Rejected',
            Order::STATUS_DELIVERED => 'Delivered',
            Order::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown'
        };
    }
}