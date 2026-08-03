@extends('layouts.app')
@section('title', $restaurant->nom)

@section('content')
<div class="bg-gray-50 min-h-screen">
    {{-- Bannière du restaurant --}}
    <div class="relative h-64 md:h-80 overflow-hidden">
        @if($restaurant->image)
            <img src="{{ $restaurant->image }}" alt="{{ $restaurant->nom }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500"></div>
        @endif
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl font-bold mb-2">{{ $restaurant->nom }}</h1>
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="text-yellow-400 text-xl">★</span>
                        <span class="font-bold">{{ number_format($restaurant->note_moyenne ?? 0, 1) }}</span>
                        <span class="text-sm opacity-90">({{ $restaurant->avis->count() }} avis)</span>
                    </div>
                    <span class="opacity-90">•</span>
                    <span>{{ $restaurant->type_cuisine }}</span>
                    <span class="opacity-90">•</span>
                    <span class="flex items-center gap-2">
                        🚚 {{ $restaurant->frais_livraison == 0 ? 'Livraison gratuite' : $restaurant->frais_livraison . ' FCFA' }}
                    </span>
                    <span class="opacity-90">•</span>
                    <span class="flex items-center gap-2">
                        ⏱️ {{ $restaurant->temps_livraison_moyen ?? 30 }}-{{ ($restaurant->temps_livraison_moyen ?? 30) + 15 }} min
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Colonne principale - Menu --}}
            <div class="lg:col-span-2">
                {{-- Catégories --}}
                @if($categories->count() > 0)
                    <div class="flex gap-2 overflow-x-auto pb-4 mb-6">
                        <button wire:click="setCategory(null)"
                                class="px-4 py-2 rounded-full whitespace-nowrap transition {{ !isset($selectedCategory) ? 'bg-orange-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                            Tout le menu
                        </button>
                        @foreach($categories as $categorie)
                            <button wire:click="setCategory({{ $categorie->id }})"
                                    class="px-4 py-2 rounded-full whitespace-nowrap transition {{ (isset($selectedCategory) && $selectedCategory == $categorie->id) ? 'bg-orange-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                                {{ $categorie->nom }}
                            </button>
                        @endforeach
                    </div>
                @endif
                
                {{-- Produits par catégorie --}}
                @forelse($produitsParCategorie as $categorie => $produits)
                    <div class="mb-8" wire:key="categorie-{{ $categorie }}">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $categorie }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($produits as $produit)
                                <x-card-produit :produit="$produit" />
                            @endforeach
                        </div>
                    </div>
                @empty
                    <x-empty-state 
                        icon="🍴"
                        title="Aucun produit disponible"
                        message="Le menu de ce restaurant est temporairement indisponible"
                    />
                @endforelse
            </div>
            
            {{-- Sidebar - Panier --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    @livewire('client.cart', ['restaurantId' => $restaurant->id])
                </div>
            </div>
        </div>
        
        {{-- Informations du restaurant --}}
        <div class="mt-12 bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📖 À propos</h2>
            <p class="text-gray-600 mb-4">{{ $restaurant->description }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">🕒 Horaires</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        @foreach($restaurant->horaires as $horaire)
                            <li>{{ $horaire->jour }}: {{ $horaire->heure_ouverture }} - {{ $horaire->heure_fermeture }}</li>
                        @endforeach
                    </ul>
                </div>
                
                @if($restaurant->adresse)
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">📍 Adresse</h3>
                    <p class="text-gray-600">
                        {{ $restaurant->adresse->quartier ?? '' }}<br>
                        {{ $restaurant->adresse->ville ?? '' }}<br>
                        {{ $restaurant->adresse->pays ?? 'Côte d\'Ivoire' }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        
        {{-- Avis --}}
        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">⭐ Avis des clients</h2>
            
            @if($restaurant->avis->count() === 0)
                <p class="text-gray-500 text-center py-8">Aucun avis pour le moment</p>
            @else
                <div class="space-y-4">
                    @foreach($restaurant->avis->take(5) as $avis)
                        <div class="border-b border-gray-100 pb-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center font-bold text-orange-600">
                                        {{ substr($avis->client->utilisateur->nom, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $avis->client->utilisateur->nom }}</p>
                                        <p class="text-xs text-gray-500">{{ $avis->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $avis->note ? 'text-yellow-500' : 'text-gray-300' }}">★</span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-gray-600">{{ $avis->commentaire }}</p>
                            
                            @if($avis->reponse)
                                <div class="mt-3 ml-12 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-700"><strong>Réponse du restaurant:</strong> {{ $avis->reponse->contenu }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Écouter les événements temps réel pour le panier
    window.Echo?.private(`cart.{{ auth()->id() }}`)
        .listen('.cart-updated', (data) => {
            Livewire.dispatch('refresh');
        });
</script>
@endpush
@endsection
