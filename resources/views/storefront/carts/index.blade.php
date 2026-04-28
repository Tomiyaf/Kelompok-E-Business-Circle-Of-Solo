@extends('layouts.app')

@section('title', 'Shopping Bag - Sanctum')

@section('content')
<div class="min-h-screen pt-40 pb-32 px-6 bg-luxury-cream">
	<div class="max-w-5xl mx-auto">
		<header class="mb-16">
			<a href="{{ route('products.index') }}" class="text-[10px] uppercase tracking-widest text-luxury-gold flex items-center group transition-colors hover:text-luxury-charcoal mb-8">
				<i data-lucide="arrow-left" class="mr-3 transition-transform group-hover:-translate-x-1" style="stroke-width:1.5"></i>
				Continue Shopping
			</a>
			<h1 class="text-4xl md:text-6xl font-serif font-light mb-2">Shopping Bag</h1>
			<p class="text-luxury-charcoal/50 text-sm tracking-wide">
				{{ $items->isEmpty() ? 'Your bag is currently empty.' : $totalQuantity . ' items in your bag.' }}
			</p>
		</header>

		@if ($items->isNotEmpty())
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
				<div class="lg:col-span-2 space-y-8">
					@foreach ($items as $item)
						@php
							$product = $item->productVariant->product;
							$imageUrl = $product->images->first()?->image_url;
							$displayImage = $imageUrl
								? (\Illuminate\Support\Str::startsWith($imageUrl, ['http://', 'https://', '/'])
									? $imageUrl
									: asset('storage/' . ltrim($imageUrl, '/')))
								: null;
							$unitPrice = (float) ($item->productVariant->price ?? 0);
						@endphp
						<div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 pb-8 border-b border-luxury-charcoal/10">
							<div class="w-24 h-32 bg-luxury-charcoal/5 flex-shrink-0">
								@if ($displayImage)
									<img src="{{ $displayImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover mix-blend-multiply opacity-80" />
								@else
									<div class="w-full h-full flex items-center justify-center text-xs text-luxury-charcoal/40">No Image</div>
								@endif
							</div>
							<div class="flex-grow space-y-2">
								<p class="text-[9px] uppercase tracking-widest text-luxury-gold font-bold">{{ $product->brand->name ?? 'Unknown Brand' }}</p>
								<h3 class="text-xl font-serif">{{ $product->name }}</h3>
								<p class="text-xs text-luxury-charcoal/60 uppercase tracking-widest">{{ $item->productVariant->name ?? 'Variant' }}</p>
							</div>

							<div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-12 w-full sm:w-auto mt-4 sm:mt-0">
								<div class="flex items-center border border-luxury-charcoal/20 bg-transparent">
									<form method="POST" action="{{ route('cart.items.update', $item) }}">
										@csrf
										@method('PATCH')
										<input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
										<button type="submit" class="p-3 hover:text-luxury-gold transition-colors" aria-label="Decrease quantity">
											<i data-lucide="minus" class="w-3 h-3"></i>
										</button>
									</form>
									<span class="w-8 text-center text-xs font-mono">{{ $item->quantity }}</span>
									<form method="POST" action="{{ route('cart.items.update', $item) }}">
										@csrf
										@method('PATCH')
										<input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
										<button type="submit" class="p-3 hover:text-luxury-gold transition-colors" aria-label="Increase quantity">
											<i data-lucide="plus" class="w-3 h-3"></i>
										</button>
									</form>
								</div>

								<div class="flex items-center justify-between w-full sm:w-auto sm:block text-right">
									<p class="font-mono text-lg">${{ number_format($unitPrice * $item->quantity, 0, '.', ',') }}.00</p>
									<form method="POST" action="{{ route('cart.items.destroy', $item) }}" class="sm:mt-2">
										@csrf
										@method('DELETE')
										<button type="submit" class="text-luxury-charcoal/40 hover:text-red-500 transition-colors" aria-label="Remove item">
											<i data-lucide="trash-2" class="w-4 h-4" style="stroke-width:1.5"></i>
										</button>
									</form>
								</div>
							</div>
						</div>
					@endforeach
				</div>

				<div class="lg:col-span-1">
					<div class="bg-white p-8 shadow-sm border border-luxury-gold/10 sticky top-32">
						<h3 class="font-serif text-2xl mb-8 border-b border-luxury-charcoal/10 pb-4">Order Summary</h3>
						<div class="space-y-4 text-sm font-light mb-8">
							<div class="flex justify-between">
								<span class="text-luxury-charcoal/60">Subtotal</span>
								<span class="font-mono">${{ number_format($totalPrice, 0, '.', ',') }}.00</span>
							</div>
							<div class="flex justify-between">
								<span class="text-luxury-charcoal/60">Shipping</span>
								<span class="font-mono tracking-tighter text-luxury-gold text-xs uppercase">Complimentary</span>
							</div>
						</div>
						<div class="flex justify-between items-center border-t border-luxury-charcoal/10 pt-6 mb-8">
							<span class="uppercase tracking-[0.2em] text-[10px] font-bold">Total</span>
							<span class="font-mono text-2xl">${{ number_format($totalPrice, 0, '.', ',') }}.00</span>
						</div>
						<button class="luxury-button w-full">
							Proceed to Checkout
						</button>
					</div>
				</div>
			</div>
		@else
			<div class="text-center py-20 bg-white/50 border border-luxury-gold/10 mt-8">
				<p class="font-serif text-2xl mb-6 text-luxury-charcoal/50 italic">Your bag is awaiting an addition.</p>
				<a href="{{ route('products.index') }}" class="inline-block border-b border-luxury-charcoal pb-1 text-sm uppercase tracking-widest hover:text-luxury-gold hover:border-luxury-gold transition-colors">
					Explore Collection
				</a>
			</div>
		@endif
	</div>
</div>
@endsection
