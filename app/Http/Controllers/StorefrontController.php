<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Scent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $products = Product::query()
            ->with(['images', 'variants', 'brand'])
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('storefront.home', [
            'products' => $products,
        ]);
    }

    public function catalog(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $categoryIds = collect((array) $request->input('category', []))
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->values()
            ->all();
        $brandIds = collect((array) $request->input('brand', []))
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->values()
            ->all();
        $scentIds = collect((array) $request->input('scent', []))
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->values()
            ->all();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        $scents = Scent::query()
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with(['images', 'variants', 'brand', 'category', 'scents'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(!empty($categoryIds), function ($query) use ($categoryIds): void {
                $query->whereIn('category_id', $categoryIds);
            })
            ->when(!empty($brandIds), function ($query) use ($brandIds): void {
                $query->whereIn('brand_id', $brandIds);
            })
            ->when(!empty($scentIds), function ($query) use ($scentIds): void {
                $query->whereHas('scents', function ($scentQuery) use ($scentIds): void {
                    $scentQuery->whereIn('scents.id', $scentIds);
                });
            })
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('storefront.products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'scents' => $scents,
            'search' => $search,
            'selectedCategory' => $categoryIds,
            'selectedBrand' => $brandIds,
            'selectedScent' => $scentIds,
        ]);
    }

    public function detail(Product $product): View
    {
        $product->load(['images', 'variants', 'brand', 'category', 'scents']);

        $relatedProducts = Product::query()
            ->with(['images', 'variants', 'brand'])
            ->where('id', '!=', $product->id)
            ->when($product->category_id, function ($query) use ($product): void {
                $query->where('category_id', $product->category_id);
            })
            ->latest('created_at')
            ->take(4)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $relatedProducts = Product::query()
                ->with(['images', 'variants', 'brand'])
                ->where('id', '!=', $product->id)
                ->latest('created_at')
                ->take(4)
                ->get();
        }

        return view('storefront.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function about(): View
    {
        return view('storefront.about.index');
    }

    public function contact(): View
    {
        return view('storefront.contact.index');
    }
}
