@props(['restaurant', 'size' => 'default'])

@php
    $sizeClasses = [
        'small' => 'max-w-xs',
        'default' => 'max-w-sm',
        'large' => 'max-w-md'
    ];
@endphp

<div class="{{ $sizeClasses[$size] ?? $sizeClasses['default'] }} bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
    {{-- Image du restaurant --}}
    <div class="relative h-48 overflow-hidden">
        @if($restaurant->image)
            <img src="{{ $restaurant->image }}" alt="{{ $restaurant->nom }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                <span class="text-6xl">🍽️</span>
            </div>
        @endif
        
        {{-- Badge statut --}}
        <div class="absolute top-3 right-3">
            @if($restaurant->est_ouvert)
                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    Ouvert
                </span>
            @else
                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    Fermé
                </span>
            @endif
        </div>
        
        {{-- Temps de livraison --}}
        @if(isset($restaurant->temps_livraison_moyen))
        <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-medium text-gray-700">
            ⏱️ {{ $restaurant->temps_livraison_moyen }} min
        </div>
        @endif
    </div>
    
    {{-- Contenu --}}
    <div class="p-4">
        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $restaurant->nom }}</h3>
        
        @if($restaurant->description)
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($restaurant->description, 80) }}</p>
        @endif
        
        {{-- Type de cuisine --}}
        @if($restaurant->type_cuisine)
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach(explode(',', $restaurant->type_cuisine) as $type)
                    <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded-md text-xs font-medium">
                        {{ trim($type) }}
                    </span>
                @endforeach
            </div>
        @endif
        
        {{-- Note et avis --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="text-yellow-500 text-lg">★</span>
                <span class="font-bold text-gray-800">{{ number_format($restaurant->note_moyenne ?? 0, 1) }}</span>
                <span class="text-gray-500 text-sm">({{ $restaurant->nombre_avis ?? 0 }} avis)</span>
            </div>
            
            @if(isset($restaurant->frais_livraison))
                <span class="text-sm text-gray-600">
                    @if($restaurant->frais_livraison == 0)
                        🚚 Livraison gratuite
                    @else
                        🚚 {{ number_format($restaurant->frais_livraison, 0) }} FCFA
                    @endif
                </span>
            @endif
        </div>
        
        {{-- Adresse --}}
        @if($restaurant->adresse)
            <div class="flex items-center text-gray-500 text-sm mb-4">
                <span class="mr-2">📍</span>
                <span class="line-clamp-1">{{ $restaurant->adresse->quartier ?? '' }}{{ isset($restaurant->adresse->ville) ? ', ' . $restaurant->adresse->ville : '' }}</span>
            </div>
        @endif
        
        {{-- Bouton d'action --}}
        <a href="{{ route('client.restaurants.show', $restaurant->id) }}" 
           class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-orange-700 transition-colors text-center block">
            Voir le menu
        </a>
    </div>
</div>
