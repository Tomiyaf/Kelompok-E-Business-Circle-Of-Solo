@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 transition-all px-4 py-2 font-bold uppercase tracking-widest text-[10px] focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none';

    $variantClasses = [
        'primary' => 'bg-[var(--color-primary)] text-white hover:bg-[#222] focus:ring-[var(--color-primary)] border border-[var(--color-primary)]',
        'secondary' => 'bg-[var(--color-secondary)] text-[var(--color-primary)] hover:bg-[#b5952f] focus:ring-[var(--color-secondary)] border border-[var(--color-secondary)]',
        'outline' => 'border border-[var(--color-primary)] bg-transparent hover:bg-[var(--color-primary)] hover:text-white focus:ring-[var(--color-primary)] text-[var(--color-primary)]',
        'ghost' => 'bg-transparent hover:bg-gray-100 text-[var(--color-primary)] border-b-2 border-transparent hover:border-[var(--color-primary)]',
        'danger' => 'bg-red-50 text-red-600 hover:bg-red-100 focus:ring-red-500 border border-red-100',
    ];

    $resolvedVariantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $baseClasses.' '.$resolvedVariantClass]) }}>
    {{ $slot }}
</button>
