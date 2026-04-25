@extends('layouts.admin')

@section('title', 'Kelola Orders')

@section('content')
	@php
		$orderMap = $orders->getCollection()->mapWithKeys(function ($order) {
			return [$order->id => [
				'id' => $order->id,
				'created_at' => optional($order->created_at)?->format('d/m/Y') ?? '-',
				'status' => $order->status,
				'total_price' => (float) $order->total_price,
				'shipping_cost' => (float) $order->shipping_cost,
				'user' => [
					'name' => $order->user?->name,
					'email' => $order->user?->email,
					'phone' => $order->user?->phone,
				],
				'items' => $order->items->map(function ($item) {
					return [
						'id' => $item->id,
						'product_name' => $item->productVariant?->product?->name ?? 'Unknown',
						'variant_name' => $item->productVariant?->name ?? '-',
						'quantity' => $item->quantity,
						'price' => (float) $item->price,
					];
				})->values()->toArray(),
			]];
		})->toArray();
	@endphp

	<div x-data="ordersPage({ orders: @js($orderMap) })" class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 shadow-sm">
			<div>
				<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Orders</h2>
				<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Manage customer orders</p>
			</div>
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
			<form method="GET" action="{{ route('admin.orders.index') }}" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 justify-between items-center bg-gray-50/50">
				<div class="relative w-full sm:w-72">
					<i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
					<input type="text"
						   name="search"
						   placeholder="Search by Order ID or Customer..."
						   value="{{ $filters['search'] ?? '' }}"
						   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-[var(--color-primary)]">
				</div>
				<select name="status"
						onchange="this.form.submit()"
						class="w-full sm:w-auto px-4 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-[var(--color-primary)]">
					<option value="">All Status</option>
					@foreach($statuses as $status)
						<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
					@endforeach
				</select>
			</form>

			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
					<tr class="border-b-2 border-gray-100">
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Order ID</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Customer</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
						<th class="px-6 py-4 font-medium text-right">Actions</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					@forelse($orders as $order)
						@php
							$status = $order->status;
							$statusVariant = match ($status) {
								'pending' => 'warning',
								'paid' => 'info',
								'completed' => 'success',
								'cancelled' => 'danger',
								default => 'default',
							};
							$displayTotal = (float) $order->total_price + (float) $order->shipping_cost;
						@endphp
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4 font-medium text-[var(--color-primary)]">#{{ $order->id }}</td>
							<td class="px-6 py-4 text-gray-500 text-sm">{{ optional($order->created_at)?->format('d/m/Y') ?? '-' }}</td>
							<td class="px-6 py-4 font-medium text-gray-900">{{ $order->user?->name ?? 'Unknown' }}</td>
							<td class="px-6 py-4 font-medium">Rp {{ number_format($displayTotal, 0, ',', '.') }}</td>
							<td class="px-6 py-4">
								<x-admin.ui.badge variant="{{ $statusVariant }}" class="capitalize">
									{{ $status }}
								</x-admin.ui.badge>
							</td>
							<td class="px-6 py-4 text-right">
								<x-admin.ui.button variant="ghost" class="px-2 py-1" @click="openDetail({{ $order->id }})">
									<i data-lucide="eye" class="w-4 h-4 mr-1"></i>
									Detail
								</x-admin.ui.button>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="px-6 py-8 text-center text-gray-400">No orders found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($orders->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $orders->links() }}
				</div>
			@endif
		</x-admin.ui.card>

		<x-admin.modal open="isDetailOpen" titleVar="modalTitle" maxWidth="max-w-3xl">
			<template x-if="selectedOrder">
				<div class="space-y-6">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-sm text-gray-500 mb-1">Customer</p>
							<div class="font-medium text-gray-900" x-text="selectedOrder.user?.name || '-' "></div>
							<div class="text-sm text-gray-500" x-text="selectedOrder.user?.email || '-' "></div>
							<div class="text-sm text-gray-500" x-text="selectedOrder.user?.phone || '-' "></div>
						</div>
						<div class="text-right">
							<p class="text-sm text-gray-500 mb-1">Order Status</p>
							<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider"
								  :class="statusClass(selectedOrder.status)"
								  x-text="selectedOrder.status"></span>
						</div>
					</div>

					<div class="border border-gray-200 rounded-lg overflow-hidden">
						<table class="w-full text-left text-sm">
							<thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
							<tr>
								<th class="px-4 py-2 font-medium">Item</th>
								<th class="px-4 py-2 font-medium">Variant</th>
								<th class="px-4 py-2 font-medium text-right">Qty</th>
								<th class="px-4 py-2 font-medium text-right">Price</th>
							</tr>
							</thead>
							<tbody class="divide-y divide-gray-100">
							<template x-for="item in selectedOrder.items" :key="item.id">
								<tr>
									<td class="px-4 py-3 font-medium text-gray-900" x-text="item.product_name"></td>
									<td class="px-4 py-3 text-gray-600" x-text="item.variant_name"></td>
									<td class="px-4 py-3 text-right" x-text="item.quantity"></td>
									<td class="px-4 py-3 text-right" x-text="formatCurrency(item.price)"></td>
								</tr>
							</template>
							</tbody>
						</table>
					</div>

					<div class="space-y-2 text-sm ml-auto w-56">
						<div class="flex justify-between">
							<span class="text-gray-500">Subtotal</span>
							<span class="font-medium" x-text="formatCurrency(selectedOrder.total_price)"></span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-500">Shipping</span>
							<span class="font-medium" x-text="formatCurrency(selectedOrder.shipping_cost)"></span>
						</div>
						<div class="flex justify-between pt-2 border-t border-gray-200 text-base">
							<span class="font-bold">Total</span>
							<span class="font-bold" x-text="formatCurrency((selectedOrder.total_price || 0) + (selectedOrder.shipping_cost || 0))"></span>
						</div>
					</div>

					<div class="border-t border-gray-200 pt-6 mt-6">
						<p class="text-sm font-medium text-gray-700 mb-3">Update Order Status</p>

						<form x-ref="statusForm" method="POST" :action="statusFormAction" class="hidden">
							@csrf
							@method('PATCH')
							<input type="hidden" name="status" x-model="nextStatus">
						</form>

						<div class="flex flex-wrap gap-2">
							@foreach(['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'] as $status)
								<x-admin.ui.button type="button"
												   variant="outline"
												   class="capitalize"
												   @click="submitStatus('{{ $status }}')"
												   x-bind:class="selectedOrder && selectedOrder.status === '{{ $status }}' ? 'bg-[var(--color-primary)] text-white' : ''">
									{{ $status }}
								</x-admin.ui.button>
							@endforeach
						</div>
					</div>
				</div>
			</template>
		</x-admin.modal>
	</div>

	<script>
		function ordersPage(config) {
			return {
				orders: config.orders || {},
				selectedOrder: null,
				isDetailOpen: false,
				modalTitle: 'Order Details',
				statusFormAction: '',
				nextStatus: '',

				openDetail(orderId) {
					const key = String(orderId);
					const found = this.orders[key] ?? this.orders[orderId] ?? null;
					if (!found) {
						return;
					}

					this.selectedOrder = found;
					this.isDetailOpen = true;
					this.modalTitle = `Order Details #${found.id}`;
					this.statusFormAction = `{{ url('admin/orders') }}/${found.id}/status`;
				},

				statusClass(status) {
					switch (status) {
						case 'pending': return 'bg-[#D4AF37]/10 text-[#D4AF37]';
						case 'paid': return 'bg-blue-100 text-blue-800';
						case 'processing': return 'bg-blue-100 text-blue-800';
						case 'shipped': return 'bg-indigo-100 text-indigo-800';
						case 'completed': return 'bg-green-100 text-green-800';
						case 'cancelled': return 'bg-red-100 text-red-800';
						default: return 'bg-gray-100 text-gray-800';
					}
				},

				submitStatus(status) {
					if (!this.selectedOrder) {
						return;
					}

					this.nextStatus = status;
					this.$nextTick(() => {
						this.$refs.statusForm.submit();
					});
				},

				formatCurrency(value) {
					const num = Number(value || 0);
					return `Rp ${new Intl.NumberFormat('id-ID').format(num)}`;
				},
			};
		}
	</script>
@endsection
