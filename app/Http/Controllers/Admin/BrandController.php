<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $brands = Brand::query()->orderBy('name')->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($brands);
        }

        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands/logos', 'public');
            $validated['logo_url'] = Storage::url($path);
        }

        unset($validated['logo']);

        $brand = Brand::create($validated);

        if ($request->expectsJson()) {
            return response()->json($brand, 201);
        }

        return back()->with('success', 'Brand berhasil ditambahkan.');
    }

    public function update(Request $request, Brand $brand): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $this->deleteLocalPublicFile($brand->logo_url);
            $path = $request->file('logo')->store('brands/logos', 'public');
            $validated['logo_url'] = Storage::url($path);
        }

        unset($validated['logo']);

        $brand->update($validated);

        if ($request->expectsJson()) {
            return response()->json($brand);
        }

        return back()->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(Request $request, Brand $brand): RedirectResponse|JsonResponse
    {
        $this->deleteLocalPublicFile($brand->logo_url);
        $brand->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Brand berhasil dihapus.']);
        }

        return back()->with('success', 'Brand berhasil dihapus.');
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
