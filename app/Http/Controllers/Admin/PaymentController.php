<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Payment::query()
            ->with(['order:id,user_id,total_price,status', 'order.user:id,name,email'])
            ->orderByDesc('id');

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($builder) use ($search): void {
                $builder->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('order_id', $search);
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($payments);
        }

        return view('admin.payments.index', [
            'payments' => $payments,
            'filters' => $request->only(['payment_status', 'search']),
        ]);
    }
}
