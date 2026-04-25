<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $statusKeys = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];

        $orderStatusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusSummary = [];
        foreach ($statusKeys as $status) {
            $statusSummary[$status] = (int) ($orderStatusCounts[$status] ?? 0);
        }

        $salesChart = Order::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_orders, COALESCE(SUM(total_price), 0) as total_revenue')
            ->whereNotNull('created_at')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        $now = Carbon::now();
        $currentPeriodStart = $now->copy()->subDays(29)->startOfDay();
        $previousPeriodStart = $currentPeriodStart->copy()->subDays(30);
        $previousPeriodEnd = $currentPeriodStart->copy()->subSecond();

        $currentPeriodRevenue = (float) Order::query()
            ->whereBetween('created_at', [$currentPeriodStart, $now])
            ->sum('total_price');

        $previousPeriodRevenue = (float) Order::query()
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('total_price');

        $revenueGrowthPercent = 0.0;
        if ($previousPeriodRevenue > 0) {
            $revenueGrowthPercent = (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100;
        }

        $totalOrders = Order::count();
        $totalRevenue = (float) Order::sum('total_price');
        $pendingOrders = (int) ($statusSummary['pending'] ?? 0);
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $data = [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'total_customers' => User::where('role', 'customer')->count(),
            'order_status_summary' => $statusSummary,
            'sales_chart' => $salesChart,
            'total_order_items' => OrderItem::sum('quantity'),
            'pending_orders' => $pendingOrders,
            'average_order_value' => $averageOrderValue,
            'current_period_revenue' => $currentPeriodRevenue,
            'previous_period_revenue' => $previousPeriodRevenue,
            'revenue_growth_percent' => $revenueGrowthPercent,
        ];

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('admin.dashboard.index', $data);
    }
}
