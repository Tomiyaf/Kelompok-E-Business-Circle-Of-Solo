<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', "Sanctum"))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest" defer></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }

        .noise-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.04;
            background-image: radial-gradient(circle at 1px 1px, rgba(0, 0, 0, 0.35) 1px, transparent 0);
            background-size: 3px 3px;
        }
    </style>
</head>
<body class="font-sans min-h-screen bg-[var(--color-background)] text-[var(--color-primary)]" x-data="{ isOpen: false }" x-init="$nextTick(() => window.lucide && window.lucide.createIcons())">
<div class="flex flex-col min-h-screen relative">
    <div class="noise-overlay"></div>

    @php
        $navLinks = [
            ['name' => 'Home', 'route' => 'home', 'active' => fn () => request()->routeIs('home')],
            ['name' => 'Collections', 'route' => 'products.index', 'active' => fn () => request()->routeIs('products.*')],
            ['name' => 'About', 'route' => 'about', 'active' => fn () => request()->routeIs('about')],
            ['name' => 'Contact', 'route' => 'contact', 'active' => fn () => request()->routeIs('contact')],
        ];

        $dashboardRoute = auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : null;
        $cartRoute = auth()->check() ? route('cart.index') : route('login');
        $cartCount = auth()->check()
            ? \App\Models\CartItem::query()
                ->whereHas('cart', fn ($query) => $query->where('user_id', auth()->id()))
                ->sum('quantity')
            : 0;
    @endphp

    <nav class="fixed top-6 left-6 right-6 z-50 h-20 px-8 md:px-12 flex items-center justify-between rounded-full glass-nav">
        <a href="{{ route('home') }}" class="text-xl md:text-2xl font-light tracking-[0.4em] font-serif uppercase text-luxury-charcoal">
            Sanctum
        </a>

        <div class="hidden md:flex items-center space-x-12">
            @foreach($navLinks as $link)
                @php
                    $active = $link['active']();
                @endphp
                <a href="{{ route($link['route']) }}"
                   class="text-[9px] uppercase tracking-[0.3em] font-medium transition-all duration-500 hover:text-luxury-gold relative group {{ $active ? 'text-luxury-gold' : 'text-luxury-charcoal/60' }}">
                    {{ $link['name'] }}
                    <span class="absolute -bottom-1 left-0 h-[1px] bg-luxury-gold transition-all duration-500 group-hover:w-full {{ $active ? 'w-full' : 'w-0' }}"></span>
                </a>
            @endforeach
        </div>

        <div class="hidden md:flex items-center space-x-6">
            <button type="button" class="hover:text-luxury-gold transition-all duration-300 transform hover:scale-110 text-luxury-charcoal" aria-label="Search">
                <i data-lucide="search" class="w-[18px] h-[18px]" style="stroke-width:1"></i>
            </button>

            <a href="{{ $cartRoute }}" class="hover:text-luxury-gold transition-all duration-300 transform hover:scale-110 relative group text-luxury-charcoal" aria-label="Cart">
                <i data-lucide="shopping-bag" class="w-[18px] h-[18px]" style="stroke-width:1"></i>
                @if($cartCount > 0)
                    <span class="absolute -top-1 -right-2 w-4 h-4 bg-luxury-gold text-white text-[8px] rounded-full flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>

            <div class="w-px h-4 bg-luxury-charcoal/20"></div>

            @auth
                @if($dashboardRoute)
                    <a href="{{ $dashboardRoute }}" class="text-[9px] uppercase tracking-[0.22em] font-semibold text-luxury-charcoal/70 hover:text-luxury-gold transition-colors whitespace-nowrap">
                        Dashboard
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline-flex items-center">
                    @csrf
                    <button type="submit" class="inline-flex items-center text-[9px] uppercase tracking-[0.22em] font-semibold text-luxury-charcoal/70 hover:text-luxury-gold transition-colors whitespace-nowrap leading-none">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-[9px] uppercase tracking-[0.22em] font-semibold text-luxury-charcoal/70 hover:text-luxury-gold transition-colors whitespace-nowrap">
                    Sign In
                </a>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="text-[9px] uppercase tracking-[0.22em] font-semibold px-4 py-2 border border-luxury-charcoal/30 text-luxury-charcoal/70 hover:border-luxury-gold hover:text-luxury-gold transition-all duration-300 whitespace-nowrap">
                        Register
                    </a>
                @endif
            @endauth
        </div>

        <button class="md:hidden text-luxury-charcoal p-2 rounded-full hover:bg-black/5 transition-colors" @click="isOpen = !isOpen" aria-label="Toggle menu">
            <i data-lucide="menu" class="w-5 h-5" style="stroke-width:1" x-show="!isOpen" x-cloak></i>
            <i data-lucide="x" class="w-5 h-5" style="stroke-width:1" x-show="isOpen" x-cloak></i>
        </button>

        <div class="md:hidden overflow-hidden bg-luxury-cream absolute top-20 left-0 w-full border-t border-luxury-gold/10"
             x-show="isOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2">
            <div class="flex flex-col items-center justify-center min-h-[calc(100vh-80px)] space-y-8 p-6">
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       @click="isOpen = false"
                       class="text-xl font-serif text-luxury-charcoal hover:text-luxury-gold tracking-[0.2em] uppercase">
                        {{ $link['name'] }}
                    </a>
                @endforeach

                <a href="{{ $cartRoute }}" @click="isOpen = false" class="text-xl font-serif text-luxury-charcoal hover:text-luxury-gold tracking-[0.2em] uppercase">
                    Shopping Bag
                </a>

                <div class="w-12 h-px bg-luxury-charcoal/20"></div>

                @auth
                    @if($dashboardRoute)
                        <a href="{{ $dashboardRoute }}" @click="isOpen = false" class="text-sm uppercase tracking-[0.2em] text-luxury-charcoal/70 hover:text-luxury-gold">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" @submit="isOpen = false" class="w-full flex justify-center">
                        @csrf
                        <button type="submit" class="inline-flex items-center text-sm uppercase tracking-[0.2em] text-luxury-charcoal/70 hover:text-luxury-gold leading-none">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" @click="isOpen = false" class="text-sm uppercase tracking-[0.2em] text-luxury-charcoal/70 hover:text-luxury-gold">Sign In</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" @click="isOpen = false" class="text-sm uppercase tracking-[0.2em] px-4 py-2 border border-luxury-charcoal/30 text-luxury-charcoal/70 hover:border-luxury-gold hover:text-luxury-gold transition-all duration-300">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow relative z-10">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <footer class="w-full px-6 md:px-12 py-8 flex flex-col md:flex-row justify-between items-center text-[9px] uppercase tracking-[0.3em] opacity-40 border-t border-luxury-gold/10 mt-auto bg-luxury-cream relative z-10">
        <div>© 2026 Sanctum Reseller</div>
        <div class="flex space-x-8 mt-4 md:mt-0">
            <a href="#" class="hover:text-luxury-gold transition-colors">Instagram</a>
            <a href="#" class="hover:text-luxury-gold transition-colors">Pinterest</a>
            <a href="#" class="hover:text-luxury-gold transition-colors">Legal</a>
        </div>
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });

    document.addEventListener('alpine:navigated', () => {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
</script>
</body>
</html>
