<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $totalSales = (float) Order::sum('total_price');

        $bestSellingProducts = OrderItem::query()
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->selectRaw('products.id, products.name, SUM(order_items.quantity) as total_quantity')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $ordersPerDay = Order::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_orders, COALESCE(SUM(total_price), 0) as total_revenue')
            ->whereNotNull('created_at')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        $data = [
            'total_sales' => $totalSales,
            'best_selling_products' => $bestSellingProducts,
            'orders_per_day' => $ordersPerDay,
        ];

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('admin.reports.index', $data);
    }
}
