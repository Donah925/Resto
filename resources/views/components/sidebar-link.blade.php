@props(['href', 'icon', 'active' => false])

@php
    $classes = $active
        ? 'bg-orange-600 text-white'
        : 'text-gray-300 hover:bg-gray-800 hover:text-white';
@endphp

<a href="{{ $href }}" class="flex items-center px-6 py-3 text-sm {{ $classes }} transition-colors">
    <span class="mr-3">{{ $icon }}</span>
    <span class="flex-1">{{ $slot }}</span>
</a>
