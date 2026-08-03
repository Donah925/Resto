@props(['id', 'title' => null, 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ][$size] ?? 'max-w-lg';
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
         onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>

    {{-- Modal panel --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-xl {{ $sizes }} w-full overflow-hidden">
            {{-- Header --}}
            @if($title)
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h3 class="text-xl font-bold text-gray-800">{{ $title }}</h3>
                    <button onclick="document.getElementById('{{ $id }}').classList.add('hidden')" 
                            class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
            @endif

            {{-- Content --}}
            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                {{ $slot }}
            </div>

            {{-- Footer (optionnel) --}}
            @if(isset($footer))
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fermer avec Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('{{ $id }}').classList.add('hidden');
        }
    });
</script>
@endpush
