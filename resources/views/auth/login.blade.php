<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In - Sanctum</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        .group:focus-within .group-focus-within\:text-luxury-gold {
            color: var(--color-secondary);
        }

        .group:focus-within .group-focus-within\:border-luxury-gold {
            border-color: var(--color-secondary);
        }
    </style>
</head>
<body class="font-sans min-h-screen bg-luxury-cream text-luxury-charcoal">
<div class="flex flex-col min-h-screen relative">
    <div class="noise-overlay"></div>

    <main class="flex-grow relative z-10 flex items-center justify-center">
        <div class="w-full min-h-screen pt-40 pb-32 px-6 flex flex-col items-center justify-center">
            <div class="w-full max-w-md">
                <div class="text-center mb-16">
                    <h1 class="text-4xl md:text-5xl font-serif font-light mb-6 relative inline-block">
                        Sign In
                        <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-12 h-[1px] bg-luxury-gold"></div>
                    </h1>
                    <p class="mt-8 text-sm text-luxury-charcoal/60 font-light">
                        Enter your email and password to access your account.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 border border-red-200/30 bg-red-50/50 text-red-700 text-sm rounded-sm">
                        <ul class="list-disc pl-5 space-y-1 text-[9px]">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-10">
                    @csrf

                    <div class="space-y-2 group">
                        <label for="email" class="block text-[9px] uppercase tracking-[0.3em] font-medium text-luxury-charcoal/80 group-focus-within:text-luxury-gold transition-colors">
                            Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full bg-transparent border-b border-luxury-charcoal/20 py-3 text-luxury-charcoal placeholder-luxury-charcoal/30 focus:outline-none group-focus-within:border-luxury-gold transition-colors font-light text-sm"
                            placeholder="you@example.com"
                        />
                        @error('email')
                            <p class="text-[9px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 group">
                        <div class="flex justify-between items-center">
                            <label for="password" class="block text-[9px] uppercase tracking-[0.3em] font-medium text-luxury-charcoal/80 group-focus-within:text-luxury-gold transition-colors">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="inline-block text-[9px] uppercase tracking-[0.2em] text-luxury-charcoal/40 hover:text-luxury-gold transition-colors">
                                    Forgot?
                                </a>
                            @endif
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full bg-transparent border-b border-luxury-charcoal/20 py-3 text-luxury-charcoal placeholder-luxury-charcoal/30 focus:outline-none group-focus-within:border-luxury-gold transition-colors font-light text-sm"
                            placeholder="••••••••"
                        />
                        @error('password')
                            <p class="text-[9px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="luxury-button w-full">
                        Sign In
                    </button>
                </form>

                <div class="mt-12 text-center">
                    <p class="text-xs text-luxury-charcoal/60 font-light">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-luxury-charcoal font-medium hover:text-luxury-gold transition-colors">
                            Create one
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
</script>
</body>
</html>
