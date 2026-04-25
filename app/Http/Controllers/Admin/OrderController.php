<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUS_FLOW = [
        'pending' => ['paid', 'cancelled'],
        'paid' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function index(Request $request): View|JsonResponse
    {
        $query = Order::query()
            ->with([
                'user:id,name,email,phone,address',
                'items:id,order_id,product_variant_id,quantity,price',
                'items.productVariant:id,product_id,name,price,stock',
                'items.productVariant.product:id,name',
            ])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($builder) use ($search): void {
                $builder->where('id', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($orders);
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => array_keys(self::STATUS_FLOW),
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(Request $request, Order $order): View|JsonResponse
    {
        $order->load([
            'user:id,name,email,phone,address',
            'items.productVariant.product',
            'payment',
        ]);

        if ($request->expectsJson()) {
            return response()->json($order);
        }

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUS_FLOW))],
        ]);

        $newStatus = $validated['status'];
        $currentStatus = (string) $order->status;

        if ($currentStatus !== $newStatus && ! in_array($newStatus, self::STATUS_FLOW[$currentStatus] ?? [], true)) {
            $message = "Transisi status dari {$currentStatus} ke {$newStatus} tidak valid.";

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->withErrors(['status' => $message]);
        }

        $order->update(['status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json($order->fresh(['user:id,name,email']));
        }

        return back()->with('success', 'Status order berhasil diperbarui.');
    }
}
