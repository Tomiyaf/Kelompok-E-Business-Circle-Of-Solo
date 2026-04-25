@extends('layouts.admin')

@section('title', 'Kelola Scents')

@section('content')
	@php
		$scentMap = $scents->getCollection()
			->mapWithKeys(function ($scent) {
				return [$scent->id => [
					'id' => $scent->id,
					'name' => $scent->name,
				]];
			})
			->toArray();
	@endphp

	<div x-data="scentPage({
			hasErrors: @js($errors->any()),
			oldName: @js(old('name')),
			oldMethod: @js(old('_method')),
			oldScentId: @js(old('context_scent_id')),
			scentMap: @js($scentMap)
		})"
		 x-init="initialize()"
		 class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 shadow-sm">
			<div>
				<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Scents</h2>
				<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Manage perfume scent profiles</p>
			</div>

			<x-admin.ui.button type="button"
							   variant="primary"
							   @click="openCreate()"
							   class="w-full sm:w-auto">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Add Scent
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
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Scent Name</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Created At</th>
						<th class="px-6 py-4 font-medium text-right">Actions</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					@forelse($scents as $scent)
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4 font-medium text-gray-900">{{ $scent->name }}</td>
							<td class="px-6 py-4 text-gray-500 text-sm">{{ optional($scent->created_at)?->format('d/m/Y') ?? '-' }}</td>
							<td class="px-6 py-4 text-right">
								<div class="inline-flex items-center gap-1">
									<x-admin.ui.button type="button"
													   variant="ghost"
													   @click="openEdit({{ $scent->id }})"
													   class="px-2 py-1 text-gray-500 hover:text-blue-600">
										<i data-lucide="edit-2" class="w-4 h-4"></i>
									</x-admin.ui.button>

									<form method="POST" action="{{ route('admin.scents.destroy', $scent) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this scent?')">
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
							<td colspan="3" class="px-6 py-8 text-center text-gray-400">No scents found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($scents->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $scents->links() }}
				</div>
			@endif
		</x-admin.ui.card>

		<x-admin.modal open="isModalOpen" titleVar="modalTitle">
			<form :action="formAction" method="POST" class="space-y-4" @submit="isSubmitting = true">
				@csrf

				<template x-if="formMethod !== 'POST'">
					<input type="hidden" name="_method" :value="formMethod">
				</template>

				<input type="hidden" name="context_scent_id" :value="scentId">

				<div class="w-full space-y-1 my-3 focus-within:text-[var(--color-primary)]">
					<label class="block text-[10px] font-bold uppercase tracking-widest text-[#2C2C2C] transition-colors">Scent Name</label>
					<input type="text"
						   name="name"
						   x-model="name"
						   placeholder="e.g. Woody, Floral..."
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
    <span x-text="isSubmitting ? 'Saving...' : 'Save Scent'"></span>
</x-admin.ui.button>
				</div>
			</form>
		</x-admin.modal>
	</div>

	<script>
		function scentPage(config) {
			return {
				isModalOpen: false,
				isSubmitting: false,
				formAction: '{{ route('admin.scents.store') }}',
				formMethod: 'POST',
				modalTitle: 'Add Scent',
				scentId: null,
				name: '',
				scentMap: config.scentMap || {},

				initialize() {
					if (config.hasErrors) {
						if (config.oldMethod === 'PUT' && config.oldScentId && this.scentMap[config.oldScentId]) {
							this.openEdit(config.oldScentId);
						} else {
							this.openCreate();
						}

						this.name = config.oldName || this.name;
					}
				},

				openCreate() {
					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Add Scent';
					this.formAction = '{{ route('admin.scents.store') }}';
					this.formMethod = 'POST';
					this.scentId = null;
					this.name = '';
				},

				openEdit(id) {
					const scent = this.scentMap[id];
					if (!scent) {
						return;
					}

					this.isModalOpen = true;
					this.isSubmitting = false;
					this.modalTitle = 'Edit Scent';
					this.formAction = `{{ url('admin/scents') }}/${id}`;
					this.formMethod = 'PUT';
					this.scentId = id;
					this.name = scent.name || '';
				},

				closeModal() {
					this.isModalOpen = false;
					this.isSubmitting = false;
				},
			};
		}
	</script>
@endsection
