@extends('layouts.app')

@section('title', "Collections - Sanctum")

@section('content')
<div class="pt-48 pb-40 px-6 md:px-24 max-w-[1800px] mx-auto animate-fade-in relative">
    <div class="absolute top-40 right-0 w-1/4 h-screen bg-luxury-gold/5 -z-10 blur-3xl opacity-30"></div>

    <div class="max-w-7xl mx-auto" x-data="{ open: false }">
        <header class="flex flex-col md:flex-row justify-between items-end mb-12 border-b border-luxury-gold/20 pb-12">
            <div class="space-y-4">
                <span class="text-luxury-gold text-[9px] font-bold tracking-[0.5em] uppercase">Discovery</span>
                <h1 class="text-5xl md:text-8xl font-serif font-light">The <span class="italic">Gallery</span></h1>
            </div>
            @php
                $activeFiltersCount = count($selectedCategory ?? []) + count($selectedBrand ?? []) + count($selectedScent ?? []);
            @endphp
            <div class="flex items-center gap-6 mt-12 md:mt-0 text-[10px] uppercase tracking-[0.3em] font-bold">
                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-2 text-luxury-charcoal hover:text-luxury-gold transition-colors"
                >
                    <i data-lucide="sliders-horizontal" class="w-4 h-4" style="stroke-width:1"></i>
                    <span>Filters @if($activeFiltersCount > 0) ({{ $activeFiltersCount }}) @endif</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" style="stroke-width:1"></i>
                </button>
            </div>
        </header>

        <section class="mb-20">
            <form method="GET" action="{{ route('products.index') }}" class="overflow-hidden">
                 <div x-show="open"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 -translate-y-6"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-6"
                     class="overflow-hidden mb-20">
                    <div class="bg-white/50 backdrop-blur-md border border-luxury-gold/10 p-8 md:p-12">
                        <div class="flex flex-wrap justify-between items-center gap-4 mb-8 pb-4 border-b border-luxury-gold/10">
                            <h2 class="font-serif text-2xl italic text-luxury-charcoal">Refine Search</h2>
                            @if($activeFiltersCount > 0)
                                <a href="{{ route('products.index') }}" class="text-[9px] uppercase tracking-[0.2em] text-luxury-charcoal/50 hover:text-luxury-charcoal flex items-center gap-1">
                                    <i data-lucide="x" class="w-3 h-3" style="stroke-width:1"></i>
                                    Clear All
                                </a>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                            <div>
                                <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] mb-6 text-luxury-charcoal/40">Category</h3>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($categories as $category)
                                        @php
                                            $isChecked = in_array((int) $category->id, $selectedCategory ?? [], true);
                                            $categoryId = 'category-' . $category->id;
                                        @endphp
                                        <div class="inline-flex">
                                            <input id="{{ $categoryId }}" type="checkbox" name="category[]" value="{{ $category->id }}" class="sr-only peer" @checked($isChecked) />
                                            <label for="{{ $categoryId }}" class="px-4 py-2 border text-[9px] uppercase tracking-[0.2em] transition-all duration-300 cursor-pointer border-luxury-charcoal/20 text-luxury-charcoal/60 hover:border-luxury-charcoal hover:text-luxury-charcoal peer-checked:border-luxury-charcoal peer-checked:bg-luxury-charcoal peer-checked:text-white">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-xs text-luxury-charcoal/50">No categories yet.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] mb-6 text-luxury-charcoal/40">Brand</h3>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($brands as $brand)
                                        @php
                                            $isChecked = in_array((int) $brand->id, $selectedBrand ?? [], true);
                                            $brandId = 'brand-' . $brand->id;
                                        @endphp
                                        <div class="inline-flex">
                                            <input id="{{ $brandId }}" type="checkbox" name="brand[]" value="{{ $brand->id }}" class="sr-only peer" @checked($isChecked) />
                                            <label for="{{ $brandId }}" class="px-4 py-2 border text-[9px] uppercase tracking-[0.2em] transition-all duration-300 cursor-pointer border-luxury-charcoal/20 text-luxury-charcoal/60 hover:border-luxury-charcoal hover:text-luxury-charcoal peer-checked:border-luxury-charcoal peer-checked:bg-luxury-charcoal peer-checked:text-white">
                                                {{ $brand->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-xs text-luxury-charcoal/50">No brands yet.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] mb-6 text-luxury-charcoal/40">Scent Notes</h3>
                                <div class="flex flex-wrap gap-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                    @forelse($scents as $scent)
                                        @php
                                            $isChecked = in_array((int) $scent->id, $selectedScent ?? [], true);
                                            $scentId = 'scent-' . $scent->id;
                                        @endphp
                                        <div class="inline-flex">
                                            <input id="{{ $scentId }}" type="checkbox" name="scent[]" value="{{ $scent->id }}" class="sr-only peer" @checked($isChecked) />
                                            <label for="{{ $scentId }}" class="px-4 py-2 border text-[9px] uppercase tracking-[0.2em] transition-all duration-300 cursor-pointer border-luxury-charcoal/20 text-luxury-charcoal/60 hover:border-luxury-charcoal hover:text-luxury-charcoal peer-checked:border-luxury-charcoal peer-checked:bg-luxury-charcoal peer-checked:text-white">
                                                {{ $scent->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-xs text-luxury-charcoal/50">No scents yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-luxury-gold/10 flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-[10px] uppercase tracking-[0.28em] font-bold border border-luxury-charcoal/20 text-luxury-charcoal/70 hover:text-luxury-charcoal hover:border-luxury-charcoal/50 hover:-translate-y-0.5 transition-all duration-300">
                                Reset Filters
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-[10px] uppercase tracking-[0.28em] font-bold text-white bg-luxury-charcoal border border-luxury-charcoal hover:bg-luxury-gold hover:border-luxury-gold hover:text-luxury-charcoal shadow-[0_14px_30px_-14px_rgba(0,0,0,0.55)] hover:shadow-[0_18px_38px_-14px_rgba(212,175,55,0.45)] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        @if ($products->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-12 gap-y-32">
                @foreach ($products as $product)
                    <div class="group cursor-pointer">
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="relative aspect-[4/5] bg-white flex items-center justify-center transition-all duration-700 group-hover:bg-luxury-cream p-10 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.1)] group-hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.2)] group-hover:-translate-y-2 border border-luxury-gold/5 group-hover:border-luxury-gold/20">
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pattern-dots"></div>
                                @if ($product->images->isNotEmpty())
                                    @php($imageUrl = $product->images->first()->image_url)
                                    <img
                                        src="{{ \Illuminate\Support\Str::startsWith($imageUrl, ['http://', 'https://', '/']) ? $imageUrl : asset('storage/' . ltrim($imageUrl, '/')) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover shadow-2xl transition-transform duration-[1.5s] group-hover:scale-95 z-10"
                                    />
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-sm text-gray-500">
                                        No Image Available
                                    </div>
                                @endif
                                <div class="absolute inset-0 border border-luxury-gold/0 group-hover:border-luxury-gold/10 m-4 transition-all duration-700 z-20"></div>
                            </div>

                            <div class="mt-10 space-y-3 text-center">
                                <p class="text-[8px] text-luxury-gold font-bold tracking-[0.5em] uppercase">{{ $product->brand->name ?? 'Unknown Brand' }}</p>
                                <h3 class="font-serif text-2xl font-light group-hover:italic transition-all duration-500 px-4">{{ $product->name }}</h3>
                                <div class="flex items-center justify-center space-x-3 opacity-30 group-hover:opacity-100 transition-opacity duration-500">
                                    <span class="w-6 h-[1px] bg-luxury-gold"></span>
                                    <p class="text-[9px] text-luxury-charcoal font-medium tracking-widest">${{ number_format($product->variants->first()->price ?? 0, 0, '.', ',') }}.00</p>
                                    <span class="w-6 h-[1px] bg-luxury-gold"></span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            @if ($products->hasPages())
                <div class="mt-16">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-40">
                <p class="font-serif italic text-2xl text-luxury-charcoal/30">No fragrances found in this collection.</p>
            </div>
        @endif
    </div>
</div>
@endsection