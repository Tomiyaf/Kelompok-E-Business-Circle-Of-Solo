<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingMethodController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $shippingMethods = ShippingMethod::query()->orderBy('name')->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($shippingMethods);
        }

        return view('admin.shippings.index', compact('shippingMethods'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0'],
        ]);

        $shippingMethod = ShippingMethod::create($validated);

        if ($request->expectsJson()) {
            return response()->json($shippingMethod, 201);
        }

        return back()->with('success', 'Metode pengiriman berhasil ditambahkan.');
    }

    public function update(Request $request, ShippingMethod $shippingMethod): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0'],
        ]);

        $shippingMethod->update($validated);

        if ($request->expectsJson()) {
            return response()->json($shippingMethod);
        }

        return back()->with('success', 'Metode pengiriman berhasil diperbarui.');
    }

    public function destroy(Request $request, ShippingMethod $shippingMethod): RedirectResponse|JsonResponse
    {
        $shippingMethod->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Metode pengiriman berhasil dihapus.']);
        }

        return back()->with('success', 'Metode pengiriman berhasil dihapus.');
    }
}
