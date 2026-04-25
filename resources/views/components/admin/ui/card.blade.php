@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-100 shadow-sm '.$class]) }}>
    {{ $slot }}
</div>
