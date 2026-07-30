@props(['type' => 'info', 'message'])

@php
    $classes = [
        'success' => 'bg-green-50 border-green-500 text-green-800',
        'error' => 'bg-red-50 border-red-500 text-red-800',
        'warning' => 'bg-yellow-50 border-yellow-500 text-yellow-800',
        'info' => 'bg-blue-50 border-blue-500 text-blue-800',
    ][$type] ?? 'bg-blue-50 border-blue-500 text-blue-800';

    $icons = [
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️',
    ][$type] ?? 'ℹ️';
@endphp

<div {{ $attributes->merge(['class' => "border-l-4 p-4 mb-4 rounded $classes"]) }} x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <span class="text-xl mr-2">{{ $icons }}</span>
            <p>{{ $message }}</p>
        </div>
        <button @click="show = false" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>
</div>
