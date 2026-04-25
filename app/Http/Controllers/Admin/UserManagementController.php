<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = User::query()
            ->select(['id', 'name', 'email', 'phone', 'role', 'created_at'])
            ->orderByDesc('created_at');

        if ($request->filled('role') && $request->string('role')->value() !== 'all') {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($users);
        }

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $request->only(['role', 'search']),
        ]);
    }
}
