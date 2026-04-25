@extends('layouts.admin')

@section('title', 'Kelola Brand')

@section('content')
	@php
		$brandMap = $brands->getCollection()
			->mapWithKeys(function ($brand) {
				return [$brand->id => [
					'id' => $brand->id,
					'name' => $brand->name,
					'logo_url' => $brand->logo_url,
				]];
			})
			->toArray();
	@endphp

	<div x-data="brandPage({
			hasErrors: @js($errors->any()),
			oldName: @js(old('name')),
			oldLogoUrl: @js(old('logo_url')),
			oldMethod: @js(old('_method')),
			oldBrandId: @js(old('context_brand_id')),
			brandMap: @js($brandMap)
		})"
		 x-init="initialize()"
		 class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 shadow-sm">
			<div>
				<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Brands</h2>
				<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Manage perfume brands</p>
			</div>
			<x-admin.ui.button type="button"
							variant="primary"
							@click="openCreate()"
							class="w-full sm:w-auto">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Add New Brand
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
						<th class="px-6 py-4 font-medium w-24">Logo</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Brand Name</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Created At</th>
						<th class="px-6 py-4 font-medium text-right">Actions</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					@forelse($brands as $brand)
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4">
								<div class="w-12 h-12 rounded bg-white border border-gray-200 p-1 flex items-center justify-center overflow-hidden">
									@if($brand->logo_url)
										<img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="max-w-full max-h-full object-contain">
									@else
										<span class="text-gray-300 text-xs">N/A</span>
									@endif
								</div>
							</td>
							<td class="px-6 py-4 font-medium text-gray-900">{{ $brand->name }}</td>
							<td class="px-6 py-4 text-gray-500 text-sm">{{ optional($brand->created_at)?->format('d/m/Y') ?? '-' }}</td>
							<td class="px-6 py-4 text-right">
								<div class="inline-flex items-center gap-1">
									<x-admin.ui.button type="button"
													variant="ghost"
													@click="openEdit({{ $brand->id }})"
													class="px-2 py-1 text-gray-500 hover:text-blue-600">
										<i data-lucide="edit-2" class="w-4 h-4"></i>
									</x-admin.ui.button>

									<form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this brand?')">
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
							<td colspan="4" class="px-6 py-8 text-center text-gray-400">No brands found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($brands->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $brands->links() }}
				</div>
			@endif
		</x-admin.ui.card>

		<x-admin.modal open="isModalOpen" titleVar="modalTitle">
			<form :action="formAction" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="isSubmitting = true">
				@csrf

				<template x-if="formMethod !== 'POST'">
					<input type="hidden" name="_method" :value="formMethod">
				</template>

				<input type="hidden" name="context_brand_id" :value="brandId">

				<div class="w-full space-y-1 my-3 focus-within:text-[var(--color-primary)]">
					<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] transition-colors">Brand Name</label>
					<input type="text"
						   name="name"
						   x-model="name"
						   placeholder="e.g. Chanel"
						   required
						   class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm">
				</div>

				<div class="w-full space-y-1 my-3 focus-within:text-[var(--color-primary)]">
					<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] transition-colors">Logo URL (optional)</label>
					<input type="text"
						   name="logo_url"
						   x-model="logoUrl"
						   placeholder="https://..."
						   class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm">
				</div>

				<div class="w-full space-y-1 my-3 focus-within:text-[var(--color-primary)]">
					<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] transition-colors">Upload Logo (optional)</label>
					<input type="file"
						   name="logo"
						   accept="image/*"
						   @change="handleFileChange"
						   class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm file:mr-3 file:px-3 file:py-1.5 file:border-0 file:bg-[#0F0F0F] file:text-white file:text-xs file:uppercase file:tracking-widest file:font-bold">
					<p class="text-[10px] text-gray-400 uppercase tracking-widest">Maksimal 2MB</p>
				</div>

				<div class="pt-1" x-show="previewUrl">
					<p class="text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-2">Preview</p>
					<div class="w-20 h-20 rounded bg-white border border-gray-200 p-1 flex items-center justify-center overflow-hidden">
						<img :src="previewUrl" alt="Logo preview" class="max-w-full max-h-full object-contain">
					</div>
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
    <span x-text="isSubmitting ? 'Saving...' : 'Save Brand'"></span>
</x-admin.ui.button>
				</div>
			</form>
		</x-admin.modal>
	</div>

	<script>
		function brandPage(config) {
			return {
				isModalOpen: false,
				isSubmitting: false,
				formAction: '{{ route('admin.brands.store') }}',
				formMethod: 'POST',
				modalTitle: 'Add New Brand',
				brandId: null,
				name: '',
				logoUrl: '',
				previewUrl: '',
				brandMap: config.brandMap || {},

				initialize() {
					if (config.hasErrors) {
						if (config.oldMethod === 'PUT' && config.oldBrandId && this.brandMap[config.oldBrandId]) {
							this.openEdit(config.oldBrandId);
						} else {
							this.openCreate();
						}

						this.name = config.oldName || this.name;
						this.logoUrl = config.oldLogoUrl || this.logoUrl;
						this.previewUrl = this.logoUrl || this.previewUrl;
					}
				},

				openCreate() {
					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Add New Brand';
					this.formAction = '{{ route('admin.brands.store') }}';
					this.formMethod = 'POST';
					this.brandId = null;
					this.name = '';
					this.logoUrl = '';
					this.previewUrl = '';
				},

				openEdit(id) {
					const brand = this.brandMap[id];
					if (!brand) {
						return;
					}

					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Edit Brand';
					this.formAction = `{{ url('admin/brands') }}/${id}`;
					this.formMethod = 'PUT';
					this.brandId = id;
					this.name = brand.name || '';
					this.logoUrl = brand.logo_url || '';
					this.previewUrl = brand.logo_url || '';
				},

				closeModal() {
					this.isModalOpen = false;
					this.isSubmitting = false;
				},

				handleFileChange(event) {
					const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
					if (file) {
						this.previewUrl = URL.createObjectURL(file);
					} else {
						this.previewUrl = this.logoUrl || '';
					}
				},
			};
		}
	</script>
@endsection
