@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => "flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 " . ($active ? 'bg-teal-500/10 text-teal-400 border-l-4 border-teal-500' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800')]) }}
    wire:navigate
>
    @if($icon)
        {!! $icon !!}
    @endif
    <span class="font-medium truncate transition-all duration-300 overflow-hidden whitespace-nowrap" x-show="!$store.sidebar.collapsed" x-transition:enter="opacity-0" x-transition:enter-end="opacity-100">
        {{ $slot }}
    </span>
</a>