@props([
    'open' => 'modalOpen',
    'title' => 'Modal',
    'titleVar' => null,
    'maxWidth' => 'max-w-md',
])

<div x-show="{{ $open }}"
     x-cloak
     x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
     @keydown.escape.window="{{ $open }} = false">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="{{ $open }} = false"></div>

    <div x-show="{{ $open }}"
         x-transition
         class="bg-white border border-[#2C2C2C] shadow-2xl relative w-full sm:w-full {{ $maxWidth }} transform transition-all my-auto max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-[#F8F8F8] shrink-0">
            @if($titleVar)
                <h3 class="text-xl font-serif text-[#0F0F0F] font-bold" x-text="{{ $titleVar }}"></h3>
            @else
                <h3 class="text-xl font-serif text-[#0F0F0F] font-bold">{{ $title }}</h3>
            @endif
            <button type="button"
                    @click="{{ $open }} = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
