@extends('layouts.admin')

@section('title', 'Kelola Products')

@section('content')
	@php
		$productMap = $products->getCollection()
			->mapWithKeys(function ($product) {
				return [$product->id => [
					'id' => $product->id,
					'name' => $product->name,
					'description' => $product->description,
					'brand_id' => $product->brand_id,
					'category_id' => $product->category_id,
					'created_at' => optional($product->created_at)?->format('d/m/Y') ?? '-',
					'brand_name' => $product->brand?->name,
					'category_name' => $product->category?->name,
					'images' => $product->images->map(fn ($image) => [
						'id' => $image->id,
						'image_url' => $image->image_url,
					])->values()->toArray(),
					'variants' => $product->variants->map(fn ($variant) => [
						'id' => $variant->id,
						'name' => $variant->name,
						'price' => $variant->price,
						'stock' => $variant->stock,
					])->values()->toArray(),
					'scent_ids' => $product->scents->pluck('id')->values()->toArray(),
					'variants_count' => $product->variants_count,
				]];
			})
			->toArray();
	@endphp

	<div x-data="productsPage({
			products: @js(array_values($productMap)),
			productMap: @js($productMap),
			brands: @js($brands),
			categories: @js($categories),
			scents: @js($scents),
			hasErrors: @js($errors->any()),
			oldMethod: @js(old('_method')),
			oldProductId: @js(old('context_product_id')),
			oldName: @js(old('name')),
			oldDescription: @js(old('description')),
			oldBrandId: @js(old('brand_id')),
			oldCategoryId: @js(old('category_id')),
			oldScentIds: @js(old('scent_ids', [])),
			oldVariants: @js(old('variants', [])),
			oldRemoveImageIds: @js(old('remove_image_ids', [])),
			oldImageUrls: @js(old('image_urls', []))
		})"
		 x-init="initialize()"
		 x-effect="$nextTick(() => window.initLucideIcons && window.initLucideIcons())"
		 class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 shadow-sm">
			<div>
				<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Products</h2>
				<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Manage catalog and product details</p>
			</div>

			<x-admin.ui.button type="button"
							   variant="primary"
							   @click="openCreate()"
							   class="w-full sm:w-auto">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Add Product
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

		<x-admin.ui.card>
			<div class="p-4 border-b border-gray-100 bg-gray-50/50">
				<div class="relative w-full sm:w-72">
					<i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
					<input type="text"
						   placeholder="Search product name..."
						   x-model="searchTerm"
						   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-[var(--color-primary)]">
				</div>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
					<tr class="border-b-2 border-gray-100">
						<th class="px-6 py-4 font-medium w-16">Image</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Product</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Brand</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Category</th>
						<th class="px-6 py-4 font-medium text-center">Variants</th>
						<th class="px-6 py-4 font-medium text-right">Actions</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					<template x-if="filteredProducts().length === 0">
						<tr>
							<td colspan="6" class="px-6 py-8 text-center text-gray-400">No products found.</td>
						</tr>
					</template>

					<template x-for="product in filteredProducts()" :key="product.id">
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4">
								<div class="w-12 h-12 rounded bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
									<template x-if="(product.images && product.images.length) > 0">
										<img :src="product.images[0].image_url" :alt="product.name" class="w-full h-full object-cover">
									</template>
									<template x-if="!product.images || product.images.length === 0">
										<i data-lucide="image" class="text-gray-400 w-5 h-5"></i>
									</template>
								</div>
							</td>
							<td class="px-6 py-4">
								<div class="font-medium text-gray-900" x-text="product.name"></div>
								<div class="text-xs text-gray-500 truncate max-w-[200px]" x-text="product.description || '-' "></div>
							</td>
							<td class="px-6 py-4 text-gray-600" x-text="product.brand_name || '-' "></td>
							<td class="px-6 py-4 text-gray-600" x-text="product.category_name || '-' "></td>
							<td class="px-6 py-4 text-center">
								<x-admin.ui.badge variant="info" x-text="product.variants_count || 0"></x-admin.ui.badge>
							</td>
							<td class="px-6 py-4 text-right">
								<div class="inline-flex items-center gap-1">
									<x-admin.ui.button type="button"
													   variant="ghost"
													   @click="openEdit(product.id)"
													   class="px-2 py-1 text-gray-500 hover:text-blue-600">
										<i data-lucide="edit-2" class="w-4 h-4"></i>
									</x-admin.ui.button>

									<form method="POST" :action="`{{ url('admin/products') }}/${product.id}`" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
										@csrf
										@method('DELETE')
										<x-admin.ui.button type="submit" variant="ghost" class="px-2 py-1 text-gray-500 hover:text-red-600">
											<i data-lucide="trash-2" class="w-4 h-4"></i>
										</x-admin.ui.button>
									</form>
								</div>
							</td>
						</tr>
					</template>
					</tbody>
				</table>
			</div>

			@if($products->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $products->links() }}
				</div>
			@endif
		</x-admin.ui.card>

		<x-admin.modal open="isModalOpen" titleVar="modalTitle" maxWidth="max-w-5xl">
			<div class="p-4 bg-[#F8F8F8] border border-gray-200 text-[#0F0F0F] rounded-none mb-6 flex items-start gap-3">
				<span class="text-lg">ℹ️</span>
				<div>
					<p class="font-semibold text-sm">Product Form</p>
					<p class="text-xs text-gray-500 mt-1">Form ini mendukung upload multiple image, multi-select scents, dan dynamic variants.</p>
				</div>
			</div>

			<form :action="formAction" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
				@csrf

				<template x-if="formMethod !== 'POST'">
					<input type="hidden" name="_method" :value="formMethod">
				</template>

				<input type="hidden" name="context_product_id" :value="productId">

				<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
					<div class="space-y-4">
						<div>
							<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-1 transition-colors">Product Name</label>
							<input type="text" name="name" x-model="name" required class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm">
						</div>
						<div>
							<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-1 transition-colors">Description</label>
							<textarea rows="3" name="description" x-model="description" class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm"></textarea>
						</div>
						<div class="grid grid-cols-2 gap-6">
							<div>
								<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-1 transition-colors">Brand</label>
								<select name="brand_id" x-model="brandId" class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm">
									<option value="">Select Brand</option>
									@foreach($brands as $brand)
										<option value="{{ $brand->id }}">{{ $brand->name }}</option>
									@endforeach
								</select>
							</div>
							<div>
								<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-1 transition-colors">Category</label>
								<select name="category_id" x-model="categoryId" class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-gray-50/50 focus:outline-none focus:border-[var(--color-primary)] focus:bg-white transition-colors sm:text-sm">
									<option value="">Select Category</option>
									@foreach($categories as $category)
										<option value="{{ $category->id }}">{{ $category->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="space-y-4 bg-[#F8F8F8] p-6 border-l-4 border-[var(--color-secondary)]">
						<h4 class="text-[10px] uppercase font-bold tracking-widest text-[#0F0F0F] border-b border-gray-200 pb-2">Images & Scents</h4>
						<div>
							<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-1 transition-colors">Upload Images</label>
							<input type="file" name="images[]" multiple accept="image/*" @change="handleImagesChange"
								   class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-white focus:outline-none focus:border-[var(--color-primary)] transition-colors sm:text-sm file:mr-3 file:px-3 file:py-1.5 file:border-0 file:bg-[#0F0F0F] file:text-white file:text-xs file:uppercase file:tracking-widest file:font-bold">
						</div>

						<template x-if="newImagePreviews.length > 0">
							<div>
								<p class="text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-2">New Upload Preview</p>
								<div class="grid grid-cols-4 gap-2">
									<template x-for="(preview, idx) in newImagePreviews" :key="`new-${idx}`">
										<img :src="preview" class="w-full h-16 object-cover rounded border border-gray-200">
									</template>
								</div>
							</div>
						</template>

						<template x-if="existingImages.length > 0">
							<div>
								<p class="text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-2">Existing Images</p>
								<div class="grid grid-cols-3 gap-2">
									<template x-for="image in existingImages" :key="`img-${image.id}`">
										<label class="relative border border-gray-200 rounded overflow-hidden cursor-pointer">
											<img :src="image.image_url" class="w-full h-16 object-cover">
											<div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[9px] px-1 py-0.5 flex items-center gap-1">
												<input type="checkbox" name="remove_image_ids[]" :value="image.id" x-model="removeImageIds" class="w-3 h-3">
												Remove
											</div>
										</label>
									</template>
								</div>
							</div>
						</template>

						<div>
							<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] mb-2 transition-colors">Assign Scents</label>
							<div class="mb-2">
								<input type="text"
									   placeholder="Search scents..."
									   x-model="scentSearch"
									   class="w-full px-3 py-2.5 border-b-2 border-gray-200 bg-white focus:outline-none focus:border-[var(--color-primary)] transition-colors sm:text-sm" />
							</div>
							<template x-if="selectedScentIds.length > 0">
								<div class="flex flex-wrap gap-2 mb-3">
									<template x-for="scentId in selectedScentIds" :key="`chip-${scentId}`">
										<button type="button"
												class="inline-flex items-center gap-2 px-3 py-1 text-[9px] uppercase tracking-widest bg-[#0F0F0F] text-white"
												@click="toggleScent(scentId)">
												<span x-text="scentNameById(scentId)"></span>
												<span class="text-[10px]">×</span>
										</button>
									</template>
								</div>
							</template>
							<div class="max-h-48 overflow-y-auto border border-gray-200 bg-white">
								<template x-for="scent in filteredScents()" :key="`scent-${scent.id}`">
									<label class="flex items-center gap-3 px-3 py-2 border-b border-gray-100 text-[10px] uppercase tracking-widest cursor-pointer hover:bg-gray-50">
										<input type="checkbox"
												name="scent_ids[]"
												:value="scent.id"
												x-model="selectedScentIds"
												class="w-3.5 h-3.5" />
										<span x-text="scent.name"></span>
									</label>
								</template>
								<template x-if="filteredScents().length === 0">
									<p class="px-3 py-3 text-xs text-gray-400">No scents found.</p>
								</template>
							</div>
						</div>
					</div>
				</div>

				<div class="border-t border-[#2C2C2C] pt-6">
					<div class="flex justify-between items-center mb-4">
						<h4 class="font-serif font-bold text-[#0F0F0F] text-lg">Variants Configuration</h4>
						<x-admin.ui.button variant="outline" type="button" @click="addVariant()">
							<i data-lucide="plus" class="w-3.5 h-3.5"></i>
							Add Variant
						</x-admin.ui.button>
					</div>
					<table class="w-full text-sm text-left border-collapse">
						<thead class="bg-[#1A1A1A] text-white">
						<tr>
							<th class="px-3 py-3 font-bold text-[10px] uppercase tracking-widest">Variant Name (e.g. 50ml)</th>
							<th class="px-3 py-3 font-bold text-[10px] uppercase tracking-widest">Price</th>
							<th class="px-3 py-3 font-bold text-[10px] uppercase tracking-widest">Stock</th>
							<th class="px-3 py-3 w-10"></th>
						</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 border border-t-0 border-gray-100 bg-[#F8F8F8]">
						<template x-for="(variant, index) in variants" :key="`variant-${index}`">
							<tr>
								<td class="px-3 py-2">
									<input type="text" :name="`variants[${index}][name]`" x-model="variant.name" class="w-full px-2 py-1.5 border-b-2 border-gray-200 bg-white focus:outline-none focus:border-[var(--color-primary)]" placeholder="e.g. 100ml" required>
								</td>
								<td class="px-3 py-2">
									<input type="number" min="0" step="0.01" :name="`variants[${index}][price]`" x-model="variant.price" class="w-full px-2 py-1.5 border-b-2 border-gray-200 bg-white focus:outline-none focus:border-[var(--color-primary)]" placeholder="Rp 0" required>
								</td>
								<td class="px-3 py-2">
									<input type="number" min="0" :name="`variants[${index}][stock]`" x-model="variant.stock" class="w-full px-2 py-1.5 border-b-2 border-gray-200 bg-white focus:outline-none focus:border-[var(--color-primary)]" placeholder="0" required>
								</td>
								<td class="px-3 py-2 text-right">
									<x-admin.ui.button variant="ghost" class="text-red-500 p-1" type="button" @click="removeVariant(index)">
										<i data-lucide="trash-2" class="w-4 h-4"></i>
									</x-admin.ui.button>
								</td>
							</tr>
						</template>
						</tbody>
					</table>
				</div>

				<div class="pt-6 flex gap-3 justify-end border-t border-gray-100">
					<x-admin.ui.button type="button" variant="outline" @click="closeModal()">Cancel</x-admin.ui.button>
					<x-admin.ui.button type="submit" variant="primary" x-bind:disabled="isSubmitting">
						<span x-text="isSubmitting ? 'Saving...' : 'Save Product'"></span>
					</x-admin.ui.button>
				</div>
			</form>
		</x-admin.modal>
	</div>

	<script>
		function productsPage(config) {
			return {
				searchTerm: '',
				products: config.products || [],
				productMap: config.productMap || {},
				allScents: config.scents || [],
				scentSearch: '',
				isModalOpen: false,
				isSubmitting: false,
				modalTitle: 'Add Product',
				formAction: '{{ route('admin.products.store') }}',
				formMethod: 'POST',
				productId: null,
				name: '',
				description: '',
				brandId: '',
				categoryId: '',
				selectedScentIds: [],
				variants: [],
				existingImages: [],
				removeImageIds: [],
				newImagePreviews: [],

				initialize() {
					if (config.hasErrors) {
						if (config.oldMethod === 'PUT' && config.oldProductId && this.productMap[config.oldProductId]) {
							this.openEdit(config.oldProductId);
						} else {
							this.openCreate();
						}

						this.name = config.oldName || this.name;
						this.description = config.oldDescription || this.description;
						this.brandId = config.oldBrandId || this.brandId;
						this.categoryId = config.oldCategoryId || this.categoryId;
						this.selectedScentIds = (config.oldScentIds || []).map(String);

						if (Array.isArray(config.oldVariants) && config.oldVariants.length > 0) {
							this.variants = config.oldVariants.map(v => ({
								name: v?.name ?? '',
								price: v?.price ?? '',
								stock: v?.stock ?? '',
							}));
						}

						this.removeImageIds = (config.oldRemoveImageIds || []).map(id => String(id));
					}
				},

				filteredScents() {
					const keyword = (this.scentSearch || '').toLowerCase();
					if (!keyword) {
						return this.allScents;
					}
					return this.allScents.filter(scent => (scent.name || '').toLowerCase().includes(keyword));
				},

				scentNameById(id) {
					const found = this.allScents.find(scent => String(scent.id) === String(id));
					return found ? found.name : id;
				},

				toggleScent(id) {
					this.selectedScentIds = this.selectedScentIds.filter(item => String(item) !== String(id));
				},

				filteredProducts() {
					if (!this.searchTerm) {
						return this.products;
					}

					const key = this.searchTerm.toLowerCase();
					return this.products.filter(product => (product.name || '').toLowerCase().includes(key));
				},

				defaultVariant() {
					return {name: '', price: '', stock: ''};
				},

				openCreate() {
					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Add Product';
					this.formAction = '{{ route('admin.products.store') }}';
					this.formMethod = 'POST';
					this.productId = null;
					this.name = '';
					this.description = '';
					this.brandId = '';
					this.categoryId = '';
					this.selectedScentIds = [];
					this.scentSearch = '';
					this.variants = [this.defaultVariant()];
					this.existingImages = [];
					this.removeImageIds = [];
					this.newImagePreviews = [];
				},

				openEdit(id) {
					const product = this.productMap[id];
					if (!product) {
						return;
					}

					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Edit Product';
					this.formAction = `{{ url('admin/products') }}/${id}`;
					this.formMethod = 'PUT';
					this.productId = id;
					this.name = product.name || '';
					this.description = product.description || '';
					this.brandId = product.brand_id ? String(product.brand_id) : '';
					this.categoryId = product.category_id ? String(product.category_id) : '';
					this.selectedScentIds = (product.scent_ids || []).map(String);
					this.scentSearch = '';
					this.variants = (product.variants || []).length > 0
						? product.variants.map(v => ({name: v.name || '', price: v.price || '', stock: v.stock || ''}))
						: [this.defaultVariant()];
					this.existingImages = product.images || [];
					this.removeImageIds = [];
					this.newImagePreviews = [];
				},

				closeModal() {
					this.isModalOpen = false;
					this.isSubmitting = false;
				},

				addVariant() {
					this.variants.push(this.defaultVariant());
				},

				removeVariant(index) {
					this.variants.splice(index, 1);
					if (this.variants.length === 0) {
						this.variants.push(this.defaultVariant());
					}
				},

				handleImagesChange(event) {
					const files = event.target.files ? Array.from(event.target.files) : [];
					this.newImagePreviews = files.map(file => URL.createObjectURL(file));
				},
			};
		}
	</script>
@endsection
