@props([
    'currentPage' => 1,
    'lastPage' => 1,
    'onPageChange' => null
])

@if($lastPage > 1)
<div class="flex items-center justify-center gap-2 mt-6">
    {{-- Bouton Précédent --}}
    <button @if($currentPage <= 1) disabled @endif
            wire:click="{{ $onPageChange }}({{ $currentPage - 1 }})"
            class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
        ← Précédent
    </button>
    
    {{-- Numéros de page --}}
    @for($i = 1; $i <= $lastPage; $i++)
        @if($i === 1 || $i === $lastPage || abs($i - $currentPage) <= 2)
            <button wire:click="{{ $onPageChange }}({{ $i }})"
                    @if($i === $currentPage)
                        class="px-4 py-2 rounded-lg bg-orange-600 text-white font-semibold"
                    @else
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition"
                    @endif>
                {{ $i }}
            </button>
        @elseif(abs($i - $currentPage) === 3)
            <span class="px-2 text-gray-400">...</span>
        @endif
    @endfor
    
    {{-- Bouton Suivant --}}
    <button @if($currentPage >= $lastPage) disabled @endif
            wire:click="{{ $onPageChange }}({{ $currentPage + 1 }})"
            class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
        Suivant →
    </button>
</div>
@endif
