@extends('layouts.app')

@section('title', $product->name ? "{$product->name} - Sanctum" : "Product Detail - Sanctum")

@section('content')
@if (!isset($product) || !$product)
    <div class="pt-40 pb-32 px-6 text-center">
        <h1 class="text-4xl font-serif mb-8">Fragrance Not Found</h1>
        <a href="{{ route('products.index') }}" class="luxury-button inline-block">Back to Catalog</a>
    </div>
@else
    <div class="pt-20 lg:pt-20 min-h-screen bg-luxury-cream overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[calc(100vh-80px)]">
            <div class="bg-luxury-nude flex items-center justify-center p-8 lg:p-24 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 pattern-dots"></div>
                <div class="w-full max-w-lg aspect-[4/5] bg-luxury-charcoal relative shadow-2xl flex flex-col p-10 items-center justify-center group overflow-hidden">
                    @php
                        $productImageUrl = $product->images->first()?->image_url;
                        $resolvedProductImageUrl = $productImageUrl
                            ? (\Illuminate\Support\Str::startsWith($productImageUrl, ['http://', 'https://', '/'])
                                ? $productImageUrl
                                : asset('storage/' . ltrim($productImageUrl, '/')))
                            : null;
                    @endphp
                    @if ($resolvedProductImageUrl)
                        <img
                            src="{{ $resolvedProductImageUrl }}"
                            alt="{{ $product->name }}"
                            class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-40 transition-opacity duration-700"
                        />
                    @else
                        <div class="absolute inset-0 bg-gray-100 flex items-center justify-center text-sm text-gray-500">
                            No Image Available
                        </div>
                    @endif

                    <div class="relative z-10 text-center">
                        <div class="text-luxury-gold font-serif text-5xl mb-4 italic tracking-widest uppercase">
                            {{ $product->brand->name ?? 'Unknown Brand' }}
                        </div>
                        <div class="h-px w-20 bg-luxury-gold mb-6 mx-auto opacity-50"></div>
                        <div class="text-[10px] tracking-[0.5em] text-luxury-gold/70 uppercase">Artisanal Blend</div>
                    </div>
                </div>
            </div>

            @php
                $variants = $product->variants;
                $defaultVariant = $variants->first();
            @endphp
            <div class="p-8 lg:p-24 flex flex-col justify-center space-y-12 animate-fade-in" x-data="{
                variants: @js($variants->map(fn ($variant) => ['id' => $variant->id, 'name' => $variant->name, 'price' => (float) $variant->price])),
                selectedVariantId: {{ $defaultVariant?->id ?? 'null' }},
                quantity: 1,
                get selectedVariant() {
                    return this.variants.find(variant => variant.id === this.selectedVariantId);
                },
                get formattedPrice() {
                    const price = this.selectedVariant ? Number(this.selectedVariant.price) : 0;
                    return price.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }
            }">
                <a href="{{ route('products.index') }}" class="text-[10px] uppercase tracking-widest text-luxury-gold flex items-center group transition-colors hover:text-luxury-charcoal">
                    <span class="mr-3 transition-transform group-hover:-translate-x-1">←</span>
                    Back to Collection
                </a>

                <div class="space-y-6">
                    <div class="space-y-4">
                        <h1 class="text-5xl md:text-7xl font-serif font-light leading-tight">{{ $product->name }}</h1>
                        <div class="flex items-center space-x-4">
                            <p class="text-2xl text-luxury-gold font-light font-mono tracking-tighter">
                                $<span x-text="formattedPrice"></span>.00
                            </p>
                            <span class="text-[10px] uppercase tracking-[0.3em] text-luxury-charcoal/30 font-medium">Eau de Parfum</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-luxury-gold font-bold">The Scent Profile</p>
                        <p class="text-sm text-luxury-charcoal/60 leading-relaxed max-w-md font-light italic">
                            "{{ $product->description }}"
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-4 pt-4 border-t border-luxury-gold/10">
                        <p class="w-full text-[10px] uppercase tracking-[0.2em] text-luxury-charcoal/40 mb-2">Olfactory Notes</p>
                        @if ($product->scents->isNotEmpty())
                            @foreach ($product->scents as $scent)
                                <span class="text-[9px] uppercase tracking-[0.2em] px-4 py-2 bg-luxury-clay/30 text-luxury-charcoal/60 rounded-sm">
                                    {{ $scent->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-[9px] uppercase tracking-[0.2em] px-4 py-2 bg-luxury-clay/30 text-luxury-charcoal/60 rounded-sm">
                                No scent notes available
                            </span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.store') }}" class="space-y-10 max-w-md">
                    @csrf
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.3em] text-luxury-charcoal/40 font-medium mb-4">Select Volume</p>
                        <div class="flex flex-wrap gap-4">
                            @forelse ($variants as $variant)
                                @php($variantId = 'variant-' . $variant->id)
                                <div class="inline-flex">
                                    <input id="{{ $variantId }}" type="radio" name="variant_id" value="{{ $variant->id }}" class="sr-only peer" @checked($loop->first) x-model="selectedVariantId" />
                                    <label for="{{ $variantId }}" class="px-6 py-3 text-[10px] tracking-widest border transition-all duration-300 cursor-pointer border-luxury-charcoal/20 text-luxury-charcoal hover:border-luxury-gold peer-checked:border-luxury-charcoal peer-checked:bg-luxury-charcoal peer-checked:text-white">
                                        {{ $variant->name ?? 'Variant' }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-xs text-luxury-charcoal/50">No variants available.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-4 sm:space-y-0">
                        <div class="flex items-center border border-luxury-charcoal/10 bg-white">
                            <button class="p-4 hover:bg-luxury-gold hover:text-white transition-colors" type="button" @click="quantity = Math.max(1, quantity - 1)">
                                -
                            </button>
                            <span class="w-12 text-center text-xs font-mono font-bold tracking-widest" x-text="quantity"></span>
                            <button class="p-4 hover:bg-luxury-gold hover:text-white transition-colors" type="button" @click="quantity = quantity + 1">
                                +
                            </button>
                        </div>
                        <input type="hidden" name="quantity" :value="quantity">
                        <button class="flex-1 py-4 sm:py-6 px-4 border border-luxury-charcoal bg-luxury-charcoal text-white text-[10px] uppercase tracking-[0.3em] font-bold hover:bg-luxury-gold hover:border-luxury-gold transition-all duration-500 shadow-xl" @disabled($variants->isEmpty())>
                            Add to Shopping Bag
                        </button>
                    </div>

                    {{-- <div class="grid grid-cols-2 gap-4 pt-8 border-t border-luxury-gold/10">
                        <div class="text-center p-4">
                            <p class="text-[9px] uppercase tracking-widest text-luxury-charcoal/40 mb-1">Shipping</p>
                            <p class="text-[10px] uppercase tracking-tighter">Worldwide</p>
                        </div>
                        <div class="text-center p-4 border-l border-luxury-gold/10">
                            <p class="text-[9px] uppercase tracking-widest text-luxury-charcoal/40 mb-1">Authenticity</p>
                            <p class="text-[10px] uppercase tracking-tighter">100% Certified</p>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <section class="px-6 lg:px-24 pb-24">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-12">
                    <div>
                        <p class="text-[9px] uppercase tracking-[0.5em] text-luxury-gold font-bold">Related Products</p>
                        <h2 class="text-4xl font-serif font-light">You may also like</h2>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-[10px] uppercase tracking-[0.3em] font-bold text-luxury-charcoal hover:text-luxury-gold transition-colors">
                        View All
                    </a>
                </div>

                @if ($relatedProducts->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-12">
                        @foreach ($relatedProducts as $related)
                            <a href="{{ route('products.show', $related) }}" class="group block">
                                <div class="relative aspect-[4/5] bg-white flex items-center justify-center p-10 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.1)] group-hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.2)] transition-all duration-700 border border-luxury-gold/5 group-hover:border-luxury-gold/20">
                                    @if ($related->images->isNotEmpty())
                                        @php($relatedImageUrl = $related->images->first()->image_url)
                                        <img
                                            src="{{ \Illuminate\Support\Str::startsWith($relatedImageUrl, ['http://', 'https://', '/']) ? $relatedImageUrl : asset('storage/' . ltrim($relatedImageUrl, '/')) }}"
                                            alt="{{ $related->name }}"
                                            class="w-full h-full object-cover z-10"
                                        />
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-sm text-gray-500">
                                            No Image Available
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 border border-luxury-gold/0 group-hover:border-luxury-gold/10 m-4 transition-all duration-700 z-20"></div>
                                </div>
                                <div class="mt-10 text-center space-y-3">
                                    <p class="text-[8px] text-luxury-gold font-bold tracking-[0.5em] uppercase">{{ $related->brand->name ?? 'Unknown Brand' }}</p>
                                    <h3 class="font-serif text-2xl font-light group-hover:italic transition-all duration-500">{{ $related->name }}</h3>
                                    <p class="text-[9px] text-luxury-charcoal font-medium tracking-widest">${{ number_format($related->variants->first()->price ?? 0, 0, '.', ',') }}.00</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20">
                        <p class="font-serif italic text-2xl text-luxury-charcoal/30">No related fragrances available.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endif
@endsection