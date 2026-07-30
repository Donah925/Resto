@props(['produit', 'showRestaurant' => false])

<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
    {{-- Image du produit --}}
    <div class="relative h-40 overflow-hidden">
        @if($produit->image)
            <img src="{{ $produit->image }}" alt="{{ $produit->nom }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full bg-gradient-to-br from-orange-200 to-orange-300 flex items-center justify-center">
                <span class="text-5xl">🍴</span>
            </div>
        @endif
        
        {{-- Badge disponibilité --}}
        <div class="absolute top-2 right-2">
            @if($produit->est_disponible)
                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                    Disponible
                </span>
            @else
                <span class="bg-gray-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                    Indisponible
                </span>
            @endif
        </div>
    </div>
    
    {{-- Contenu --}}
    <div class="p-4">
        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $produit->nom }}</h3>
        
        @if($produit->description)
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($produit->description, 60) }}</p>
        @endif
        
        {{-- Prix --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl font-bold text-orange-600">{{ number_format($produit->prix, 0) }} FCFA</span>
            
            @if($produit->prix_promotionnel && $produit->prix_promotionnel < $produit->prix)
                <span class="text-sm text-gray-400 line-through">{{ number_format($produit->prix, 0) }}</span>
            @endif
        </div>
        
        {{-- Restaurant --}}
        @if($showRestaurant && $produit->restaurant)
            <div class="mb-3 pb-3 border-t border-gray-100 pt-3">
                <a href="{{ route('client.restaurants.show', $produit->restaurant->id) }}" 
                   class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    🏠 {{ $produit->restaurant->nom }}
                </a>
            </div>
        @endif
        
        {{-- Options disponibles --}}
        @if($produit->groupeOptions && $produit->groupeOptions->count() > 0)
            <div class="text-xs text-gray-500 mb-3">
                + {{ $produit->groupeOptions->count() }} option(s) disponible(s)
            </div>
        @endif
        
        {{-- Bouton d'action --}}
        @if($produit->est_disponible)
            <button wire:click="$dispatch('add-to-cart', { productId: {{ $produit->id }}, restaurantId: {{ $produit->restaurant_id }} })"
                    class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-orange-700 transition-colors flex items-center justify-center gap-2">
                <span>🛒</span>
                <span>Ajouter au panier</span>
            </button>
        @else
            <button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg font-semibold cursor-not-allowed">
                Indisponible
            </button>
        @endif
    </div>
</div>
