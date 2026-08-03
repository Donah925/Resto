@props([
    'model',
    'label' => null,
    'name',
    'type' => 'text',
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'icon' => null
])

@php
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-400">{{ $icon }}</span>
            </div>
        @endif
        
        @if($type === 'textarea')
            <textarea 
                id="{{ $name }}"
                name="{{ $name }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                placeholder="{{ $placeholder ?? '' }}"
                {{ $attributes->merge(['class' => 'w-full ' . ($icon ? 'pl-10' : 'pl-4') . ' pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition disabled:bg-gray-100 disabled:cursor-not-allowed']) }}
            >{{ old($errorKey, $slot) }}</textarea>
        @elseif($type === 'select')
            <select 
                id="{{ $name }}"
                name="{{ $name }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge(['class' => 'w-full ' . ($icon ? 'pl-10' : 'pl-4') . ' pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition disabled:bg-gray-100 disabled:cursor-not-allowed']) }}
            >
                {{ $slot }}
            </select>
        @else
            <input 
                type="{{ $type }}"
                id="{{ $name }}"
                name="{{ $name }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                placeholder="{{ $placeholder ?? '' }}"
                value="{{ old($errorKey, $slot) }}"
                {{ $attributes->merge(['class' => 'w-full ' . ($icon ? 'pl-10' : 'pl-4') . ' pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition disabled:bg-gray-100 disabled:cursor-not-allowed']) }}
            />
        @endif
    </div>
    
    @error($errorKey)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
