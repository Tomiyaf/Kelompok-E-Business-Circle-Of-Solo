<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $scents = Scent::query()->orderBy('name')->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($scents);
        }

        return view('admin.scents.index', compact('scents'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $scent = Scent::create($validated);

        if ($request->expectsJson()) {
            return response()->json($scent, 201);
        }

        return back()->with('success', 'Scent berhasil ditambahkan.');
    }

    public function update(Request $request, Scent $scent): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $scent->update($validated);

        if ($request->expectsJson()) {
            return response()->json($scent);
        }

        return back()->with('success', 'Scent berhasil diperbarui.');
    }

    public function destroy(Request $request, Scent $scent): RedirectResponse|JsonResponse
    {
        $scent->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Scent berhasil dihapus.']);
        }

        return back()->with('success', 'Scent berhasil dihapus.');
    }
}
