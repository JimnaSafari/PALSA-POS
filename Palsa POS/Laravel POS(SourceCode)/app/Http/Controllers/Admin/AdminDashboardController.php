<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    //
    public function index(){
        //Dashboard Card
        $today = Carbon::today();
        $total_sale_amt = Order::whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))
                                ->sum('totalPrice');

        $userCount = User::where('role','user')->count();//Customer Account

        $adminCount = User::where('role','admin')->orWhere('role','superadmin')->count();//Admin Account

        $orderCheck = Order::where('status','0')->groupBy('order_code')->get();

        $orderPending =count($orderCheck);

        $orderSuccess = Order::where('status','1')
                             ->groupBy('order_code')
                             ->whereDate('orders.created_at', $today)
                             ->get();

        $orderSuccess =count($orderSuccess);

        $categoryCount = Category::count();
        // $productCount = Product::count();

        $paymentType =Payment::count();

        $salesOverview = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(totalPrice) as daily_sales')
        )
            ->where('status', 1) //1 indicates completed sales
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30) // Last 30 days
            ->get();

        // dd($salesOverview);


        $topProducts = Order::select(
                                        'product_id','products.name as product_name',
                                        DB::raw('SUM(orders.count) as total_sold')
                                    )
                                        ->leftJoin('products', 'orders.product_id', '=', 'products.id')
                                        ->groupBy('product_id')
                                        ->orderBy('total_sold', 'desc')
                                        ->take(3) // Top 3 products
                                        ->get();

        // dd($topProducts);

        $stock = DB::table('products')
                    ->select('products.name', DB::raw('products.count - COALESCE(SUM(orders.count), 0) as stock'))
                    ->leftJoin('orders', function ($join) {
                        $join->on('orders.product_id', '=', 'products.id')
                            ->where('orders.status', 1);
                    })
                    ->groupBy('products.id', 'products.name', 'products.count')
                    ->get();

        $outofstock = $stock->filter(fn($item) => $item->stock < 5);
        // dd($outofstock);

        return view('admin.home',compact('total_sale_amt','userCount',
                                         'orderPending','orderSuccess',
                                         'adminCount','categoryCount',
                                         'paymentType', 'outofstock',
                                         'salesOverview', 'topProducts'
                                         ));
    }


}
