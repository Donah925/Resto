@props(['icon' => '📭', 'title', 'description' => null, 'action' => null])

<div class="text-center py-12">
    <div class="text-6xl mb-4">{{ $icon }}</div>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ $title }}</h3>
    @if($description)
        <p class="text-gray-500 mb-6">{{ $description }}</p>
    @endif
    @if($action)
        {{ $action }}
    @endif
</div>
