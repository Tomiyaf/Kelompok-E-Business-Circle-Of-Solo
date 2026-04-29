<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        window.initLucideIcons = function () {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            window.initLucideIcons();
            setTimeout(window.initLucideIcons, 50);
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-[var(--color-background)] font-sans text-sm">
<div x-data="{ sidebarOpen: false }" x-init="$nextTick(() => window.initLucideIcons && window.initLucideIcons())" class="flex">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-cloak
         x-transition.opacity
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    @php
        $navItems = [
            ['icon' => 'layout-dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
            ['icon' => 'users', 'label' => 'Users', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
            ['icon' => 'tag', 'label' => 'Brands', 'route' => 'admin.brands.index', 'active' => 'admin.brands.*'],
            ['icon' => 'grid-2x2', 'label' => 'Categories', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*'],
            ['icon' => 'sparkles', 'label' => 'Scents', 'route' => 'admin.scents.index', 'active' => 'admin.scents.*'],
            ['icon' => 'package', 'label' => 'Products', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
            ['icon' => 'archive', 'label' => 'Inventory', 'route' => 'admin.inventory.index', 'active' => 'admin.inventory.*'],
            ['icon' => 'shopping-cart', 'label' => 'Orders', 'route' => 'admin.orders.index', 'active' => 'admin.orders.*'],
            ['icon' => 'credit-card', 'label' => 'Payments', 'route' => 'admin.payments.index', 'active' => 'admin.payments.*'],
            ['icon' => 'truck', 'label' => 'Shipping', 'route' => 'admin.shipping-methods.index', 'active' => 'admin.shipping-methods.*'],
            ['icon' => 'bar-chart-3', 'label' => 'Reports', 'route' => 'admin.reports.index', 'active' => 'admin.reports.*'],
        ];
        $user = auth()->user();
        $initials = $user ? strtoupper(substr($user->name, 0, 2)) : 'AU';
    @endphp

    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 bg-[var(--color-primary)] border-r border-[#2C2C2C] text-white w-60 z-50 transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 flex flex-col shrink-0">
        <div class="p-8 flex items-center gap-3 border-b border-[#2C2C2C]">
            <div class="w-8 h-8 bg-[var(--color-secondary)] rounded-sm rotate-45 shrink-0"></div>
            <span class="text-[var(--color-secondary)] font-bold text-lg tracking-widest uppercase ml-2">Sanctum</span>
        </div>

        <nav class="flex-1 px-4 py-2 overflow-y-auto mt-2">
            <div class="space-y-1">
                @foreach($navItems as $item)
                    @php
                        $isActive = request()->routeIs($item['active']);
                        $url = route($item['route']);
                    @endphp
                    <a href="{{ $url }}"
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-sm transition-colors text-sm font-medium
                       {{ $isActive ? 'text-[var(--color-secondary)] bg-[#2C2C2C]/50 border-l-2 border-[var(--color-secondary)]' : 'text-gray-400 hover:text-white border-l-2 border-transparent' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="{{ $isActive ? 'opacity-80' : 'opacity-60' }} w-4 h-4"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>

        <div class="p-6 border-t border-[#2C2C2C] mt-auto">
            <div class="flex items-center gap-3 px-2 py-3 bg-[#1A1A1A] rounded">
                <div class="w-8 h-8 rounded-full bg-[var(--color-secondary)] flex items-center justify-center text-[var(--color-primary)] font-bold text-xs uppercase">{{ $initials }}</div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-xs font-semibold text-white truncate uppercase">{{ $user ? $user->name : 'Admin Utama' }}</p>
                    <p class="text-[10px] text-gray-500">{{ $user ? $user->email : 'admin@parfum.com' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-8 shrink-0 shadow-sm z-30">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-black p-2 -ml-2">
                    <!-- Menu icon -->
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="hidden lg:block text-lg font-serif italic font-medium tracking-tight text-[#0F0F0F]">Executive Dashboard</h1>
            </div>

            <div class="flex items-center gap-6 ml-auto">
                <div class="relative cursor-pointer">
                    <div class="absolute -top-1 -right-1 w-2 h-2 bg-[var(--color-secondary)] rounded-full"></div>
                    <span class="text-xl">🔔</span>
                </div>

                <!-- Sign out -->
                <form id="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 border border-[#0F0F0F] text-[#0F0F0F] text-xs font-bold uppercase tracking-widest hover:bg-[#0F0F0F] hover:text-white transition-all">
                        Sign Out
                    </button>
                </form>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>