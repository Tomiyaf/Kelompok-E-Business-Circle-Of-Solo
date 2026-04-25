@extends('layouts.admin')

@section('title', 'Kelola Reports')

@section('content')
	@php
		$topProducts = collect($best_selling_products ?? []);
		$ordersChart = collect($orders_per_day ?? []);

		$chartLabels = $ordersChart
			->map(fn ($row) => \Carbon\Carbon::parse($row->date)->format('d M'))
			->values();

		$chartRevenue = $ordersChart
			->map(fn ($row) => (float) $row->total_revenue)
			->values();
	@endphp

	<div class="space-y-6">
		<div>
			<h2 class="text-2xl font-serif text-[#0F0F0F] font-bold tracking-tight">Reports</h2>
			<p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Store performance analytics</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
			<x-admin.ui.card class="p-5 border-[#2C2C2C]/10">
				<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Sales</p>
				<p class="text-2xl font-bold text-[#0F0F0F] mt-2">Rp {{ number_format((float) ($total_sales ?? 0), 0, ',', '.') }}</p>
			</x-admin.ui.card>

			<x-admin.ui.card class="p-5 border-[#2C2C2C]/10">
				<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Orders (Tracked Days)</p>
				<p class="text-2xl font-bold text-[#0F0F0F] mt-2">{{ number_format((int) $ordersChart->sum('total_orders')) }}</p>
			</x-admin.ui.card>

			<x-admin.ui.card class="p-5 border-[#2C2C2C]/10">
				<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Active Report Days</p>
				<p class="text-2xl font-bold text-[#0F0F0F] mt-2">{{ number_format($ordersChart->count()) }}</p>
			</x-admin.ui.card>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
			<x-admin.ui.card class="p-6 border-[#2C2C2C]/10">
				<h3 class="text-lg font-semibold mb-4 text-gray-900 border-b border-gray-100 pb-3">Top Selling Products</h3>
				<div class="space-y-4 pt-2">
					@forelse($topProducts as $index => $item)
						<div class="flex items-center gap-4">
							<div class="w-8 h-8 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-xs shrink-0">
								{{ $index + 1 }}
							</div>
							<div class="flex-1 min-w-0">
								<p class="font-medium text-gray-900 truncate">{{ $item->name ?? 'Unknown Product' }}</p>
							</div>
							<div class="text-right">
								<p class="font-medium text-gray-900">{{ (int) $item->total_quantity }} sold</p>
							</div>
						</div>
					@empty
						<p class="text-gray-500 text-center py-4">No sales data yet.</p>
					@endforelse
				</div>
			</x-admin.ui.card>

			<x-admin.ui.card class="p-6 border-[#2C2C2C]/10">
				<h3 class="text-lg font-semibold mb-6 text-gray-900 border-b border-gray-100 pb-3">Orders Per Day (Revenue)</h3>
				<div class="h-[280px] w-full">
					@if($ordersChart->isEmpty())
						<div class="h-full flex items-center justify-center text-gray-500">No chart data yet.</div>
					@else
						<canvas id="ordersRevenueChart"></canvas>
					@endif
				</div>
			</x-admin.ui.card>
		</div>
	</div>

	@if($ordersChart->isNotEmpty())
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
		<script>
			(() => {
				const labels = @json($chartLabels);
				const revenue = @json($chartRevenue);

				const canvas = document.getElementById('ordersRevenueChart');
				if (!canvas || typeof Chart === 'undefined') {
					return;
				}

				new Chart(canvas, {
					type: 'bar',
					data: {
						labels,
						datasets: [{
							label: 'Daily Revenue',
							data: revenue,
							backgroundColor: '#D4AF37',
							borderRadius: 6,
							maxBarThickness: 40,
						}],
					},
					options: {
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: false,
							},
							tooltip: {
								callbacks: {
									label(context) {
										const value = Number(context.raw || 0);
										return `Revenue: Rp ${value.toLocaleString('id-ID')}`;
									},
								},
							},
						},
						scales: {
							x: {
								grid: {
									display: false,
								},
								ticks: {
									color: '#888',
									maxRotation: 0,
									autoSkip: true,
								},
							},
							y: {
								beginAtZero: true,
								ticks: {
									precision: 0,
									color: '#888',
									callback(value) {
										return `Rp ${Number(value).toLocaleString('id-ID')}`;
									},
								},
								grid: {
									color: '#eee',
								},
							},
						},
					},
				});
			})();
		</script>
	@endif
@endsection
