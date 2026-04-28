@extends('layouts.app')

@section('title', "Home - Sanctum")

@section('content')
<div class="pt-20">
    <section class="h-screen flex items-center bg-white relative overflow-hidden">
        <div class="absolute inset-0 bg-luxury-cream opacity-50 z-0"></div>
        <div class="absolute top-0 right-0 w-1/3 h-full bg-luxury-clay/20 -skew-x-12 transform translate-x-1/2 z-0"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 h-full w-full relative z-10">
            <div class="flex flex-col justify-center px-6 md:px-32 space-y-12">
                <div class="space-y-8">
                    <div class="flex items-center space-x-4">
                        <span class="h-px w-12 bg-luxury-gold"></span>
                        <p class="text-luxury-gold text-[9px] font-bold tracking-[0.5em] uppercase">Ethereal Fragrances</p>
                    </div>
                    <h1 class="text-6xl md:text-9xl font-serif leading-[1] font-light text-luxury-charcoal">
                        Essence of <br />
                        <span class="italic block pl-20 mt-2">Purity</span>
                    </h1>
                    <p class="text-sm text-luxury-charcoal/40 max-w-sm leading-relaxed font-light tracking-wide">
                        A masterfully curated sanctuary where rare botanical essences meet modern sophistication. Discover scents that linger as memories.
                    </p>
                </div>

                <div>
                    <a href="{{ route('products.index') }}" class="luxury-button inline-block group rounded-full">
                        Explore The Gallery
                    </a>
                </div>
            </div>

            <div class="relative hidden lg:flex items-center justify-center p-20">
                <div class="relative w-full max-w-lg aspect-[4/5] z-10">
                    <div class="absolute -inset-10 border border-luxury-gold/20 rounded-full animate-[spin_20s_linear_infinite]"></div>
                    <div class="absolute inset-0 bg-white shadow-[0_50px_100px_-20px_rgba(0,0,0,0.15)] overflow-hidden p-6">
                        <div class="w-full h-full bg-luxury-nude flex items-center justify-center relative group">
                            <img
                                src="https://images.unsplash.com/photo-1547887538-e3a2f32cb1cc?auto=format&fit=crop&q=80&w=1200"
                                alt="Signature"
                                class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-luxury-charcoal/10 mix-blend-overlay"></div>
                        </div>
                    </div>

                    <div class="absolute -bottom-10 -left-10 bg-white p-8 shadow-2xl z-20 max-w-[240px]">
                        <p class="text-luxury-gold text-[8px] font-bold tracking-[0.4em] uppercase mb-4">New Arrival</p>
                        <p class="font-serif italic text-xl text-luxury-charcoal leading-tight">Whispering <br /> Amber</p>
                        <div class="h-px w-10 bg-luxury-gold/30 my-4"></div>
                        <p class="text-[10px] text-luxury-charcoal/40 uppercase tracking-widest">Available Exclusively</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="flex justify-center -mt-10 relative z-20">
        <div class="w-px h-20 bg-gradient-to-b from-luxury-gold to-transparent"></div>
    </div>

    <section class="py-40 px-6 md:px-24 max-w-[1600px] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end mb-24 relative">
            <div class="space-y-4">
                <span class="text-luxury-gold text-[9px] font-bold tracking-[0.5em] uppercase">Private Selection</span>
                <h2 class="text-5xl md:text-7xl font-serif font-light">The Core <span class="italic">Collection</span></h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-[10px] uppercase tracking-[0.3em] font-bold text-luxury-charcoal border-b border-luxury-gold/50 pb-2 hover:border-luxury-charcoal transition-all duration-300">
                View All Curations
            </a>
        </div>

        @if ($products->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-12 gap-y-24">
                @foreach ($products->take(4) as $product)
                    <div class="group">
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="relative aspect-[4/5] bg-luxury-nude/30 overflow-hidden flex items-center justify-center p-8 group-hover:bg-luxury-clay/20 transition-all duration-700 rounded-sm">
                                <div class="absolute inset-0 border border-luxury-gold/5 m-4"></div>
                                @if ($product->images->isNotEmpty())
                                    <img
                                        src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover shadow-[0_30px_60px_-12px_rgba(0,0,0,0.15)] transition-all duration-[1s] group-hover:scale-105 group-hover:-translate-y-2"
                                    />
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-sm text-gray-500">
                                        No Image Available
                                    </div>
                                @endif

                                <div class="absolute bottom-6 left-6 right-6 translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 flex justify-center">
                                    <span class="text-[8px] uppercase tracking-[0.4em] text-luxury-gold font-bold bg-white/80 backdrop-blur-md px-6 py-3 rounded-full border border-luxury-gold/10">View Details</span>
                                </div>
                            </div>
                            <div class="mt-8 text-center space-y-2">
                                <p class="text-[8px] text-luxury-gold font-bold tracking-[0.4em] uppercase">{{ $product->brand->name ?? 'Unknown Brand' }}</p>
                                <h3 class="font-serif text-2xl font-light text-luxury-charcoal transition-colors group-hover:text-luxury-gold">{{ $product->name }}</h3>
                                <p class="text-xs text-luxury-charcoal/30 font-light tracking-widest">${{ number_format($product->variants->first()->price ?? 0, 0, '.', ',') }}.00</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-3xl border border-luxury-gold/20 bg-white/90 p-16 text-center">
                <p class="text-[10px] uppercase tracking-[0.5em] text-luxury-gold font-bold mb-4">No Products Found</p>
                <h3 class="text-3xl md:text-4xl font-serif text-luxury-charcoal mb-4">Belum ada produk di koleksi saat ini.</h3>
                <p class="text-sm text-luxury-charcoal/60 max-w-xl mx-auto mb-8">
                    Silakan kembali lagi nanti atau kunjungi halaman produk untuk melihat koleksi terbaru kami.
                </p>
                <a href="{{ route('products.index') }}" class="luxury-button inline-block rounded-full">
                    Browse All Products
                </a>
            </div>
        @endif
    </section>

    <section class="bg-luxury-nude py-32 px-6 md:px-24">
        <div class="max-w-5xl mx-auto text-center space-y-10">
            <span class="text-[10px] uppercase tracking-[0.5em] text-luxury-gold font-bold">Philosophy</span>
            <h2 class="text-4xl md:text-6xl font-serif leading-tight">
                The alchemy of scent, <br />
                <span class="italic">refined for the modern soul.</span>
            </h2>
            <p class="text-luxury-charcoal/60 leading-relaxed max-w-2xl mx-auto font-light text-sm">
                We believe fragrance is an invisible bridge between memories and dreams. Our mission is to curate scents that speak softly but linger indefinitely, using only sustainably sourced botanicals and artisanal blending techniques.
            </p>
        </div>
    </section>
</div>
@endsection