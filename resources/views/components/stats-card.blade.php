@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null])

@php
    $colors = [
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'orange' => 'from-orange-500 to-orange-600',
        'red' => 'from-red-500 to-red-600',
        'purple' => 'from-purple-500 to-purple-600',
    ][$color] ?? 'from-blue-500 to-blue-600';
@endphp

<div class="bg-white rounded-xl shadow-sm p-6 border-l-4 {{ str_replace('from-', 'border-', explode(' ', $colors)[0]) }}">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wide">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $value }}</p>
            @if($trend)
                <p class="text-sm mt-2 {{ $trend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $trend >= 0 ? '↑' : '↓' }} {{ abs($trend) }}% vs hier
                </p>
            @endif
        </div>
        <div class="bg-gradient-to-br {{ $colors }} text-white rounded-full p-4 text-2xl">
            {{ $icon }}
        </div>
    </div>
</div>
