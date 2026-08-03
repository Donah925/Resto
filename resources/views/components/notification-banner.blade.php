@props([
    'icon' => '📢',
    'title',
    'message',
    'type' => 'info', // info, success, warning, error
    'dismissible' => true
])

@php
    $styles = [
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
    ];
    
    $icons = [
        'info' => 'ℹ️',
        'success' => '✅',
        'warning' => '⚠️',
        'error' => '❌',
    ];
@endphp

<div x-data="{ shown: true }" 
     x-show="shown"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="{{ $styles[$type] }} border rounded-lg p-4 mb-4"
     role="alert">
    <div class="flex items-start gap-3">
        <span class="text-2xl">{{ $icon ?? $icons[$type] }}</span>
        <div class="flex-1">
            @if($title)
                <h4 class="font-bold mb-1">{{ $title }}</h4>
            @endif
            <p>{{ $message ?? $slot }}</p>
        </div>
        
        @if($dismissible)
            <button @click="shown = false" 
                    class="text-gray-500 hover:text-gray-700 transition-colors"
                    aria-label="Fermer">
                ✕
            </button>
        @endif
    </div>
</div>
