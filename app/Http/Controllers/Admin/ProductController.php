<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Scent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Product::query()
            ->with([
                'brand:id,name',
                'category:id,name',
                'images:id,product_id,image_url',
                'variants:id,product_id,name,price,stock',
                'scents:id,name',
            ])
            ->withCount('variants')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        $products = $query->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        return view('admin.products.index', [
            'products' => $products,
            'brands' => Brand::query()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'scents' => Scent::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search', 'brand_id', 'category_id']),
        ]);
    }

    public function show(Request $request, Product $product): View|JsonResponse
    {
        $product->load([
            'brand:id,name',
            'category:id,name',
            'scents:id,name',
            'variants',
            'images',
        ]);

        if ($request->expectsJson()) {
            return response()->json($product);
        }

        return view('admin.products.show', compact('product'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validateProductRequest($request);

        $product = DB::transaction(function () use ($request, $validated): Product {
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
            ]);

            if (! empty($validated['scent_ids'])) {
                $product->scents()->sync($validated['scent_ids']);
            }

            if (! empty($validated['variants'])) {
                foreach ($validated['variants'] as $variant) {
                    $product->variants()->create([
                        'name' => $variant['name'],
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                    ]);
                }
            }

            $this->handleProductImagesUpload($request, $product);

            return $product->load(['brand', 'category', 'scents', 'variants', 'images']);
        });

        if ($request->expectsJson()) {
            return response()->json($product, 201);
        }

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $validated = $this->validateProductRequest($request, true);

        $product = DB::transaction(function () use ($request, $validated, $product): Product {
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
            ]);

            $product->scents()->sync($validated['scent_ids'] ?? []);

            if (array_key_exists('variants', $validated)) {
                $product->variants()->delete();
                foreach ($validated['variants'] as $variant) {
                    $product->variants()->create([
                        'name' => $variant['name'],
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                    ]);
                }
            }

            if (! empty($validated['remove_image_ids'])) {
                $imagesToRemove = ProductImage::query()
                    ->where('product_id', $product->id)
                    ->whereIn('id', $validated['remove_image_ids'])
                    ->get();

                foreach ($imagesToRemove as $image) {
                    if (! $image instanceof ProductImage) {
                        continue;
                    }

                    $this->deleteLocalPublicFile($image->image_url);
                    $image->delete();
                }
            }

            $this->handleProductImagesUpload($request, $product);

            return $product->load(['brand', 'category', 'scents', 'variants', 'images']);
        });

        if ($request->expectsJson()) {
            return response()->json($product);
        }

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($product): void {
            foreach ($product->images as $image) {
                $this->deleteLocalPublicFile($image->image_url);
            }

            $product->images()->delete();
            $product->scents()->detach();
            $product->variants()->delete();
            $product->delete();
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Produk berhasil dihapus.']);
        }

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validateProductRequest(Request $request, bool $isUpdate = false): array
    {
        $requiredRule = $isUpdate ? ['required'] : ['required'];

        return $request->validate([
            'name' => array_merge($requiredRule, ['string', 'max:255']),
            'description' => ['nullable', 'string'],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'scent_ids' => ['nullable', 'array'],
            'scent_ids.*' => ['integer', Rule::exists('scents', 'id')],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'image_urls' => ['nullable', 'array'],
            'image_urls.*' => ['nullable', 'string', 'max:2048'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer', Rule::exists('product_images', 'id')],
        ]);
    }

    private function handleProductImagesUpload(Request $request, Product $product): void
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('products/images', 'public');
                $product->images()->create([
                    'image_url' => Storage::url($path),
                ]);
            }
        }

        if ($request->filled('image_urls')) {
            foreach ((array) $request->input('image_urls') as $imageUrl) {
                if (! empty($imageUrl)) {
                    $product->images()->create(['image_url' => $imageUrl]);
                }
            }
        }
    }

    private function deleteLocalPublicFile(?string $url): void
    {
        if (! $url) {
            return;
        }

        $relativePath = str_starts_with($url, '/storage/')
            ? substr($url, strlen('/storage/'))
            : (str_starts_with($url, 'storage/') ? substr($url, strlen('storage/')) : null);

        if ($relativePath) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
