<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Total sales (sum of all completed orders)
        $totalSales = Order::where('payment_status', 'paid')->sum('total_amount');

        // Orders count
        $ordersCount = Order::count();

        // Top 5 products by quantity sold
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // Active users (customers with at least 1 order)
        $activeUsers = Customer::whereHas('orders')->count();

        // Total users
        $totalUsers = Customer::count();

        // Sales by month (last 6 months)
        $salesByMonth = Order::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('payment_status', 'paid')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->map(function ($item) {
                return [
                    'month' => \Carbon\Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'total' => (float) $item->total,
                ];
            })
            ->reverse()
            ->values();
        return view('admin.analytics.index', compact(
            'totalSales',
            'ordersCount',
            'topProducts',
            'activeUsers',
            'totalUsers',
            'salesByMonth'
        ));
    }
}