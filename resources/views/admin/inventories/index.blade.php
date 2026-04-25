@extends('layouts.admin')

@section('title', 'Kelola Inventory')

@section('content')
	<div x-data="inventoryPage()" class="space-y-6">
		<div>
			<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Inventory Management</h2>
			<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Manage product variants stock</p>
		</div>

		@if(session('success'))
			<div class="px-4 py-3 border border-green-200 bg-green-50 text-green-700 text-sm rounded">
				{{ session('success') }}
			</div>
		@endif

		@if($errors->any())
			<div class="px-4 py-3 border border-red-200 bg-red-50 text-red-700 text-sm rounded">
				<ul class="list-disc pl-5 space-y-1">
					@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<x-admin.ui.card>
			<div class="p-4 border-b border-gray-100 bg-gray-50/50">
				<form method="GET" action="{{ route('admin.inventory.index') }}" class="relative w-full sm:w-80">
					<i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
					<input type="text"
						   name="search"
						   value="{{ $filters['search'] ?? '' }}"
						   placeholder="Search product/variant..."
						   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-[var(--color-primary)]">
				</form>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
					<tr class="border-b-2 border-gray-100">
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Product Name</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Variant</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Price</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock</th>
						<th class="px-6 py-4 font-medium text-right">Actions</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					@forelse($variants as $item)
						@php
							$formId = 'stock-form-'.$item->id;
						@endphp
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4 font-medium text-gray-900">{{ $item->product?->name ?? 'Unknown Product' }}</td>
							<td class="px-6 py-4 text-gray-600">{{ $item->name }}</td>
							<td class="px-6 py-4 text-gray-600">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
							<td class="px-6 py-4">
								<template x-if="editingId === '{{ $item->id }}'">
									<input type="number"
										   name="stock"
										   min="0"
										   x-model="editStock"
										   form="{{ $formId }}"
										   class="w-24 px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-[var(--color-primary)]">
								</template>
								<template x-if="editingId !== '{{ $item->id }}'">
									<span class="font-medium {{ $item->stock < 10 ? 'text-red-500' : 'text-green-600' }}">{{ $item->stock }} units</span>
								</template>
							</td>
							<td class="px-6 py-4 text-right">
								<form id="{{ $formId }}" method="POST" action="{{ route('admin.inventory.stock.update', $item) }}" class="hidden">
									@csrf
									@method('PATCH')
								</form>

								<template x-if="editingId === '{{ $item->id }}'">
									<div class="flex gap-2 justify-end">
										<x-admin.ui.button type="submit"
														   form="{{ $formId }}"
														   variant="primary"
														   class="px-3 py-1.5 text-[10px]">
											Save
										</x-admin.ui.button>
										<x-admin.ui.button type="button"
														   variant="ghost"
														   @click="cancelEdit()"
														   class="px-3 py-1.5 text-[10px]">
											Cancel
										</x-admin.ui.button>
									</div>
								</template>

								<template x-if="editingId !== '{{ $item->id }}'">
									<x-admin.ui.button type="button"
													   variant="outline"
													   @click="startEdit('{{ $item->id }}', {{ (int) $item->stock }})"
													   class="px-3 py-1.5 text-[10px]">
										Edit Stock
									</x-admin.ui.button>
								</template>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5" class="px-6 py-8 text-center text-gray-400">No inventory found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if(is_object($variants) && method_exists($variants, 'hasPages') && $variants->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $variants->links() }}
				</div>
			@endif
		</x-admin.ui.card>
	</div>

	<script>
		function inventoryPage() {
			return {
				editingId: null,
				editStock: 0,

				startEdit(id, stock) {
					this.editingId = String(id);
					this.editStock = Number.isFinite(stock) ? stock : 0;
				},

				cancelEdit() {
					this.editingId = null;
					this.editStock = 0;
				},
			};
		}
	</script>
@endsection
