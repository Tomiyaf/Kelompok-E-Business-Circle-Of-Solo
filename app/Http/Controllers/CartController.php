<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $cart = Cart::query()
            ->firstOrCreate(['user_id' => $user->id]);

        $cart->load(['items.productVariant.product.brand', 'items.productVariant.product.images']);

        $items = $cart->items;

        $totalPrice = $items->reduce(function ($total, CartItem $item) {
            $price = (float) ($item->productVariant->price ?? 0);
            return $total + ($price * $item->quantity);
        }, 0.0);

        $totalQuantity = $items->sum('quantity');

        return view('storefront.carts.index', [
            'cart' => $cart,
            'items' => $items,
            'totalPrice' => $totalPrice,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();
        $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

        $variant = ProductVariant::query()->findOrFail($data['variant_id']);

        $item = $cart->items()
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($item) {
            $item->update([
                'quantity' => $item->quantity + $data['quantity'],
            ]);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $data['quantity'],
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($cartItem->cart?->user_id !== $request->user()->id) {
            abort(403);
        }

        $cartItem->update([
            'quantity' => $data['quantity'],
        ]);

        return redirect()->route('cart.index');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart?->user_id !== $request->user()->id) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index');
    }
}
