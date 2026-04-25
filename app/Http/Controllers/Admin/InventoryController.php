<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = ProductVariant::query()
            ->with(['product:id,name'])
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search): void {
                        $productQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $variants = $query->paginate(20)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($variants);
        }

        return view('admin.inventories.index', [
            'variants' => $variants,
            'filters' => $request->only(['search']),
        ]);
    }

    public function updateStock(Request $request, ProductVariant $variant): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $variant->update([
            'stock' => $validated['stock'],
            'price' => $validated['price'] ?? $variant->price,
            'name' => $validated['name'] ?? $variant->name,
        ]);

        if ($request->expectsJson()) {
            return response()->json($variant->fresh(['product:id,name']));
        }

        return back()->with('success', 'Stok variant berhasil diperbarui.');
    }
}
