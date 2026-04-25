@extends('layouts.admin')

@section('title', 'Kelola Payments')

@section('content')
	<div class="space-y-6">
		<form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 shadow-sm">
			<div>
				<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Payments</h2>
				<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">View transaction history</p>
			</div>

			<div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
				<div class="relative w-full sm:w-72">
					<i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
					<input type="text"
						   name="search"
						   value="{{ $filters['search'] ?? '' }}"
						   placeholder="Search by transaction/order..."
						   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-[var(--color-primary)]">
				</div>

				<select name="payment_status"
						onchange="this.form.submit()"
						class="w-full sm:w-auto px-4 py-2 border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] bg-white uppercase tracking-wider text-[10px] font-bold">
					<option value="">All Status</option>
					<option value="success" @selected(($filters['payment_status'] ?? '') === 'success')>Success</option>
					<option value="pending" @selected(($filters['payment_status'] ?? '') === 'pending')>Pending</option>
					<option value="failed" @selected(($filters['payment_status'] ?? '') === 'failed')>Failed</option>
				</select>
			</div>
		</form>

		<x-admin.ui.card>
			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
					<tr class="border-b-2 border-gray-100">
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transaction ID</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Order ID</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Method</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date</th>
						<th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
					@forelse($payments as $payment)
						@php
							$status = $payment->payment_status;
							$statusVariant = match ($status) {
								'success' => 'success',
								'pending' => 'warning',
								'failed' => 'danger',
								default => 'default',
							};
							$paidAt = $payment->paid_at ? \Illuminate\Support\Carbon::parse($payment->paid_at)->format('d M Y H:i') : '-';
							$method = str_replace('_', ' ', $payment->payment_method ?? '-');
						@endphp
						<tr class="hover:bg-[#F8F8F8] transition-colors">
							<td class="px-6 py-4 font-medium text-gray-900">{{ $payment->transaction_id ?: '-' }}</td>
							<td class="px-6 py-4 text-[var(--color-secondary)] hover:underline cursor-pointer font-medium">#{{ $payment->order_id }}</td>
							<td class="px-6 py-4">
								<div class="flex items-center gap-2 text-gray-600 capitalize">
									<i data-lucide="credit-card" class="text-gray-400 w-4 h-4"></i>
									{{ $method }}
								</div>
							</td>
							<td class="px-6 py-4 text-gray-500 text-sm">{{ $paidAt }}</td>
							<td class="px-6 py-4">
								<x-admin.ui.badge variant="{{ $statusVariant }}" class="flex items-center gap-1 w-fit">
									@if($status === 'success')
										<i data-lucide="check-circle-2" class="w-3 h-3"></i>
									@elseif($status === 'pending')
										<i data-lucide="clock" class="w-3 h-3"></i>
									@elseif($status === 'failed')
										<i data-lucide="x-circle" class="w-3 h-3"></i>
									@endif
									{{ ucfirst($status) }}
								</x-admin.ui.badge>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5" class="px-6 py-8 text-center text-gray-400">No payments found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($payments->hasPages())
				<div class="px-6 py-4 border-t border-gray-100">
					{{ $payments->links() }}
				</div>
			@endif
		</x-admin.ui.card>
	</div>
@endsection
