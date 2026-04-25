@extends('layouts.admin')

@section('title', 'Kelola Categories')

@section('content')
	@php
		$categoryMap = $categories->getCollection()
			->mapWithKeys(function ($category) {
				return [$category->id => [
					'id' => $category->id,
					'name' => $category->name,
				]];
			})
			->toArray();
	@endphp

	<div x-data="categoryPage({
			hasErrors: @js($errors->any()),
			oldName: @js(old('name')),
			oldMethod: @js(old('_method')),
			oldCategoryId: @js(old('context_category_id')),
			categoryMap: @js($categoryMap)
		})"
		 x-init="initialize()"
		 class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 shadow-sm">
			<div>
				<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Categories</h2>
				<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Manage product categories</p>
			</div>

			<x-admin.ui.button type="button"
							   variant="primary"
							   @click="openCreate()"
							   class="w-full sm:w-auto">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Add Category
			</x-admin.ui.button>
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

		<x-admin.ui.card class="overflow-hidden border-[#2C2C2C]/10">
			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
					<tr class="border-b-2 border-gray-100">
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Category Name</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Created At</th>
						<th class="px-6 py-4 font-medium text-right">Actions</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					@forelse($categories as $category)
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
							<td class="px-6 py-4 text-gray-500 text-sm">{{ optional($category->created_at)?->format('d/m/Y') ?? '-' }}</td>
							<td class="px-6 py-4 text-right">
								<div class="inline-flex items-center gap-1">
									<x-admin.ui.button type="button"
													   variant="ghost"
													   @click="openEdit({{ $category->id }})"
													   class="px-2 py-1 text-gray-500 hover:text-blue-600">
										<i data-lucide="edit-2" class="w-4 h-4"></i>
									</x-admin.ui.button>

									<form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
										@csrf
										@method('DELETE')
										<x-admin.ui.button type="submit" variant="ghost" class="px-2 py-1 text-gray-500 hover:text-red-600">
											<i data-lucide="trash-2" class="w-4 h-4"></i>
										</x-admin.ui.button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="3" class="px-6 py-8 text-center text-gray-400">No categories found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($categories->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $categories->links() }}
				</div>
			@endif
		</x-admin.ui.card>

		<x-admin.modal open="isModalOpen" titleVar="modalTitle">
			<form :action="formAction" method="POST" class="space-y-4" @submit="isSubmitting = true">
				@csrf

				<template x-if="formMethod !== 'POST'">
					<input type="hidden" name="_method" :value="formMethod">
				</template>

				<input type="hidden" name="context_category_id" :value="categoryId">

				<div class="w-full space-y-1 my-3 focus-within:text-[var(--color-primary)]">
					<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] transition-colors">Category Name</label>
					<input type="text"
						   name="name"
						   x-model="name"
						   placeholder="e.g. Eau de Parfum"
						   required
						   class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm">
				</div>

				<div class="pt-4 flex gap-3 justify-end">
					<x-admin.ui.button type="button"
									   variant="outline"
									   @click="closeModal()"
									   class="border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-700">
						Cancel
					</x-admin.ui.button>

					<x-admin.ui.button type="submit"
									   variant="primary"
									   x-bind:disabled="isSubmitting">
						<span x-text="isSubmitting ? 'Saving...' : 'Save Category'"></span>
					</x-admin.ui.button>
				</div>
			</form>
		</x-admin.modal>
	</div>

	<script>
		function categoryPage(config) {
			return {
				isModalOpen: false,
				isSubmitting: false,
				formAction: '{{ route('admin.categories.store') }}',
				formMethod: 'POST',
				modalTitle: 'Add Category',
				categoryId: null,
				name: '',
				categoryMap: config.categoryMap || {},

				initialize() {
					if (config.hasErrors) {
						if (config.oldMethod === 'PUT' && config.oldCategoryId && this.categoryMap[config.oldCategoryId]) {
							this.openEdit(config.oldCategoryId);
						} else {
							this.openCreate();
						}

						this.name = config.oldName || this.name;
					}
				},

				openCreate() {
					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Add Category';
					this.formAction = '{{ route('admin.categories.store') }}';
					this.formMethod = 'POST';
					this.categoryId = null;
					this.name = '';
				},

				openEdit(id) {
					const category = this.categoryMap[id];
					if (!category) {
						return;
					}

					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Edit Category';
					this.formAction = `{{ url('admin/categories') }}/${id}`;
					this.formMethod = 'PUT';
					this.categoryId = id;
					this.name = category.name || '';
				},

				closeModal() {
					this.isModalOpen = false;
					this.isSubmitting = false;
				},
			};
		}
	</script>
@endsection
