<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $categories = Category::query()->orderBy('name', 'asc')->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($categories);
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category = Category::create($validated);

        if ($request->expectsJson()) {
            return response()->json($category, 201);
        }

        return back()->with('success', 'Category berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category->update($validated);

        if ($request->expectsJson()) {
            return response()->json($category);
        }

        return back()->with('success', 'Category berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        $category->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Category berhasil dihapus.']);
        }

        return back()->with('success', 'Category berhasil dihapus.');
    }
}
