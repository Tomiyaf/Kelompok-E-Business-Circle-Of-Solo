@props([
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'bg-gray-100 text-gray-800',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-[#D4AF37]/10 text-[#D4AF37]',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
    ];

    $resolvedVariantClass = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider '.$resolvedVariantClass]) }}>
    {{ $slot }}
</span>
