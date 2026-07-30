@props(['items' => [], 'quantities' => []])

<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">🛒 Votre Panier</h3>
    
    @if(count($items) === 0)
        <div class="text-center py-8">
            <span class="text-6xl mb-4 block">🛒</span>
            <p class="text-gray-500 mb-4">Votre panier est vide</p>
            <a href="{{ route('client.restaurants.index') }}" 
               class="inline-block bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700 transition">
                Découvrir les restaurants
            </a>
        </div>
    @else
        <div class="space-y-4">
            {{-- Liste des articles --}}
            @foreach($items as $item)
                <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
                    {{-- Image --}}
                    <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                        @if($item['image'] ?? false)
                            <img src="{{ $item['image'] }}" alt="{{ $item['nom'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-orange-100 flex items-center justify-center">
                                <span class="text-2xl">🍴</span>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Détails --}}
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">{{ $item['nom'] }}</h4>
                        
                        @if(isset($item['options']) && count($item['options']) > 0)
                            <p class="text-xs text-gray-500 mt-1">
                                {{ implode(', ', $item['options']) }}
                            </p>
                        @endif
                        
                        <p class="text-orange-600 font-bold mt-1">
                            {{ number_format($item['prix_unitaire'], 0) }} FCFA
                        </p>
                    </div>
                    
                    {{-- Quantité --}}
                    <div class="flex items-center gap-2">
                        <button wire:click="decreaseQuantity({{ $item['id'] }})"
                                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition">
                            −
                        </button>
                        <span class="w-8 text-center font-semibold">{{ $item['quantite'] }}</span>
                        <button wire:click="increaseQuantity({{ $item['id'] }})"
                                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition">
                            +
                        </button>
                    </div>
                    
                    {{-- Sous-total --}}
                    <div class="text-right">
                        <p class="font-bold text-gray-800">
                            {{ number_format($item['prix_unitaire'] * $item['quantite'], 0) }} FCFA
                        </p>
                        <button wire:click="removeItem({{ $item['id'] }})"
                                class="text-xs text-red-500 hover:text-red-700 mt-1">
                            Supprimer
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Résumé --}}
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-gray-600">
                    <span>Sous-total</span>
                    <span>{{ number_format($this->subtotal, 0) }} FCFA</span>
                </div>
                
                @if($fraisLivraison ?? 0 > 0)
                <div class="flex justify-between text-gray-600">
                    <span>Frais de livraison</span>
                    <span>{{ number_format($fraisLivraison, 0) }} FCFA</span>
                </div>
                @endif
                
                @if($reduction ?? 0 > 0)
                <div class="flex justify-between text-green-600">
                    <span>Réduction</span>
                    <span>-{{ number_format($reduction, 0) }} FCFA</span>
                </div>
                @endif
                
                <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t">
                    <span>Total</span>
                    <span>{{ number_format($this->total, 0) }} FCFA</span>
                </div>
            </div>
            
            {{-- Bouton de commande --}}
            <button wire:click="proceedToCheckout"
                    class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition-colors flex items-center justify-center gap-2">
                <span>📝</span>
                <span>Passer la commande</span>
            </button>
        </div>
    @endif
</div>
