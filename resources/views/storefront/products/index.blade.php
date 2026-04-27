@extends('layouts.app')

@section('title', "Collections - L'ESSENCE")

@section('content')
<div class="pt-48 pb-40 px-6 md:px-24 max-w-[1800px] mx-auto animate-fade-in relative">
    <div class="absolute top-40 right-0 w-1/4 h-screen bg-luxury-gold/5 -z-10 blur-3xl opacity-30"></div>

    <div class="max-w-7xl mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-end mb-32 border-b border-luxury-gold/20 pb-12">
            <div class="space-y-4">
                <span class="text-luxury-gold text-[9px] font-bold tracking-[0.5em] uppercase">Discovery</span>
                <h1 class="text-5xl md:text-8xl font-serif font-light">The <span class="italic">Gallery</span></h1>
            </div>

            @php
                $currentCategory = request('category', 'All');
            @endphp

            <div class="flex flex-wrap gap-8 md:gap-12 mt-12 md:mt-0 text-[10px] uppercase tracking-[0.3em] font-bold">
                <a href="{{ route('products.index') }}" class="transition-all duration-500 relative pb-2 {{ $currentCategory === 'All' ? 'text-luxury-charcoal' : 'text-luxury-charcoal/30 hover:text-luxury-charcoal hover:-translate-y-0.5' }}">
                    All
                    @if ($currentCategory === 'All')
                        <span class="absolute bottom-0 left-0 w-full h-[1px] bg-luxury-gold"></span>
                    @endif
                </a>

                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->name]) }}" class="transition-all duration-500 relative pb-2 {{ $currentCategory === $category->name ? 'text-luxury-charcoal' : 'text-luxury-charcoal/30 hover:text-luxury-charcoal hover:-translate-y-0.5' }}">
                        {{ $category->name }}
                        @if ($currentCategory === $category->name)
                            <span class="absolute bottom-0 left-0 w-full h-[1px] bg-luxury-gold"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </header>

        @php
            $filteredProducts = $currentCategory === 'All'
                ? $products
                : $products->filter(fn ($product) => optional($product->category)->name === $currentCategory);
        @endphp

        @if ($filteredProducts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-12 gap-y-32">
                @foreach ($filteredProducts as $product)
                    <div class="group cursor-pointer">
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="relative aspect-[4/5] bg-white flex items-center justify-center transition-all duration-700 group-hover:bg-luxury-cream p-10 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.1)] group-hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.2)] group-hover:-translate-y-2 border border-luxury-gold/5 group-hover:border-luxury-gold/20">
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pattern-dots"></div>
                                @if ($product->images->isNotEmpty())
                                    <img
                                        src="{{ asset('storage/' . $product->images->first()->image_url) }}"
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
        @else
            <div class="text-center py-40">
                <p class="font-serif italic text-2xl text-luxury-charcoal/30">No fragrances found in this collection.</p>
            </div>
        @endif
    </div>
</div>
@endsection