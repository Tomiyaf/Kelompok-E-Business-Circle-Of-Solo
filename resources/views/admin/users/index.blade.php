@extends('layouts.admin')

@section('title', 'Kelola Users')

@section('content')
	@php
		$search = $filters['search'] ?? '';
		$role = $filters['role'] ?? 'all';
	@endphp

	<div class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 shadow-sm">
			<div>
				<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Users</h2>
				<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Manage admin and customer accounts</p>
			</div>
		</div>

		<x-admin.ui.card class="overflow-hidden border-[#2C2C2C]/10">
			<form method="GET" action="{{ route('admin.users.index') }}" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 justify-between items-center bg-gray-50/50">
				<div class="relative w-full sm:w-72">
					<i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
					<input type="text"
						   name="search"
						   value="{{ $search }}"
						   placeholder="Search by name or email..."
						   class="w-full pl-10 pr-4 py-2 border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-white">
				</div>

				<div class="w-full sm:w-auto flex items-center gap-2">
					<select name="role"
							class="w-full sm:w-auto px-4 py-2 border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:bg-white bg-white uppercase tracking-wider text-[10px] font-bold">
						<option value="all" @selected($role === 'all')>All Roles</option>
						<option value="admin" @selected($role === 'admin')>Admin</option>
						<option value="customer" @selected($role === 'customer')>Customer</option>
					</select>

					<x-admin.ui.button type="submit" variant="outline" class="whitespace-nowrap">Apply</x-admin.ui.button>

					@if($search !== '' || ($role !== '' && $role !== 'all'))
						<a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 border border-gray-200 hover:bg-white transition-colors">
							Reset
						</a>
					@endif
				</div>
			</form>

			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
					<tr class="border-b-2 border-gray-100">
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Name</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Phone</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Joined Date</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					@forelse($users as $user)
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4">
								<div class="flex items-center gap-3">
									<div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-medium uppercase">
										{{ mb_substr($user->name, 0, 1) }}
									</div>
									<div>
										<div class="font-medium text-gray-900">{{ $user->name }}</div>
										<div class="text-gray-500 text-sm">{{ $user->email }}</div>
									</div>
								</div>
							</td>
							<td class="px-6 py-4">
								<x-admin.ui.badge :variant="$user->role === 'admin' ? 'warning' : 'default'">
									{{ $user->role }}
								</x-admin.ui.badge>
							</td>
							<td class="px-6 py-4 text-gray-600">{{ $user->phone ?: '-' }}</td>
							<td class="px-6 py-4 text-gray-500 text-sm">{{ optional($user->created_at)?->format('d M Y') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="4" class="px-6 py-8 text-center text-gray-400">No users found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($users->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $users->links() }}
				</div>
			@endif
		</x-admin.ui.card>
	</div>
@endsection
