@extends('layouts.app')
@section('title', 'Détails de la commande')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Bouton retour --}}
        <a href="{{ route('client.commandes.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-orange-600 mb-6 transition">
            ← Retour aux commandes
        </a>
        
        {{-- Carte principale --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            {{-- Header avec statut --}}
            <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-8 text-white">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Commande</p>
                        <h1 class="text-3xl font-bold">#{{ $commande->reference }}</h1>
                        <p class="text-sm opacity-90 mt-2">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <x-badge-statut :statut="$commande->statut" type="commande" />
                </div>
            </div>
            
            {{-- Timeline de suivi --}}
            <div class="px-6 py-8 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Suivi de votre commande</h2>
                
                <div class="relative">
                    {{-- Ligne de progression --}}
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    <div class="space-y-6 relative">
                        {{-- Étape 1: Commande passée --}}
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full {{ in_array($commande->statut, ['confirmee', 'en_cours', 'prete', 'livraison_en_cours', 'livree']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center flex-shrink-0 z-10">
                                @if(in_array($commande->statut, ['confirmee', 'en_cours', 'prete', 'livraison_en_cours', 'livree']))
                                    <span class="text-white text-sm">✓</span>
                                @else
                                    <span class="text-white text-sm">1</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">Commande confirmée</h3>
                                <p class="text-sm text-gray-500">{{ $commande->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        {{-- Étape 2: En préparation --}}
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full {{ in_array($commande->statut, ['en_cours', 'prete', 'livraison_en_cours', 'livree']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center flex-shrink-0 z-10">
                                @if(in_array($commande->statut, ['en_cours', 'prete', 'livraison_en_cours', 'livree']))
                                    <span class="text-white text-sm">✓</span>
                                @else
                                    <span class="text-white text-sm">2</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">En préparation</h3>
                                @if($commande->statut === 'en_cours' || in_array($commande->statut, ['prete', 'livraison_en_cours', 'livree']))
                                    <p class="text-sm text-green-600 font-medium">Votre commande est en cours de préparation</p>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Étape 3: Prête --}}
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full {{ in_array($commande->statut, ['prete', 'livraison_en_cours', 'livree']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center flex-shrink-0 z-10">
                                @if(in_array($commande->statut, ['prete', 'livraison_en_cours', 'livree']))
                                    <span class="text-white text-sm">✓</span>
                                @else
                                    <span class="text-white text-sm">3</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">Prête à être livrée</h3>
                                @if($commande->statut === 'prete' || in_array($commande->statut, ['livraison_en_cours', 'livree']))
                                    <p class="text-sm text-green-600 font-medium">En attente du livreur</p>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Étape 4: En livraison --}}
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full {{ in_array($commande->statut, ['livraison_en_cours', 'livree']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center flex-shrink-0 z-10">
                                @if(in_array($commande->statut, ['livraison_en_cours', 'livree']))
                                    <span class="text-white text-sm">✓</span>
                                @else
                                    <span class="text-white text-sm">4</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">En livraison</h3>
                                @if($commande->statut === 'livraison_en_cours' || $commande->statut === 'livree')
                                    <p class="text-sm text-green-600 font-medium">Votre livreur est en route</p>
                                    @if($commande->livraison && $commande->livraison->livreur)
                                        <p class="text-xs text-gray-500 mt-1">
                                            🚴 {{ $commande->livraison->livreur->utilisateur->nom }}
                                            @if($commande->livraison->livreur->telephone)
                                                - 📞 {{ $commande->livraison->livreur->telephone }}
                                            @endif
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        {{-- Étape 5: Livrée --}}
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full {{ $commande->statut === 'livree' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center flex-shrink-0 z-10">
                                @if($commande->statut === 'livree')
                                    <span class="text-white text-sm">✓</span>
                                @else
                                    <span class="text-white text-sm">5</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">Livrée</h3>
                                @if($commande->statut === 'livree')
                                    <p class="text-sm text-green-600 font-medium">Bon appétit ! 🍽️</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Détails de la commande --}}
            <div class="px-6 py-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Détails de la commande</h2>
                
                {{-- Restaurant --}}
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700 mb-2">🏠 Restaurant</h3>
                    <p class="text-gray-800">{{ $commande->restaurant->nom }}</p>
                </div>
                
                {{-- Articles --}}
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700 mb-4">📝 Articles</h3>
                    <div class="space-y-3">
                        @foreach($commande->lignes as $ligne)
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                    @if($ligne->produit->image)
                                        <img src="{{ $ligne->produit->image }}" alt="{{ $ligne->produit->nom }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-2xl">🍴</div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">{{ $ligne->produit->nom }}</p>
                                    <p class="text-sm text-gray-500">Quantité: {{ $ligne->quantite }}</p>
                                    @if($ligne->options)
                                        <p class="text-xs text-gray-500 mt-1">Options: {{ implode(', ', $ligne->options) }}</p>
                                    @endif
                                </div>
                                <p class="font-bold text-gray-800">{{ number_format($ligne->prix_unitaire * $ligne->quantite, 0) }} FCFA</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- Résumé financier --}}
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700 mb-4">💰 Résumé</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Sous-total</span>
                            <span>{{ number_format($commande->montant_total - ($commande->frais_livraison ?? 0), 0) }} FCFA</span>
                        </div>
                        @if($commande->frais_livraison > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Frais de livraison</span>
                            <span>{{ number_format($commande->frais_livraison, 0) }} FCFA</span>
                        </div>
                        @endif
                        @if($commande->reduction > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Réduction</span>
                            <span>-{{ number_format($commande->reduction, 0) }} FCFA</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t">
                            <span>Total</span>
                            <span>{{ number_format($commande->montant_total, 0) }} FCFA</span>
                        </div>
                    </div>
                </div>
                
                {{-- Paiement --}}
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700 mb-2">💳 Paiement</h3>
                    @if($commande->paiement)
                        <p class="text-gray-800">Méthode: {{ $commande->paiement->methode }}</p>
                        <p class="text-gray-800">Statut: <x-badge-statut :statut="$commande->paiement->statut" /></p>
                        @if($commande->paiement->transaction_id)
                            <p class="text-sm text-gray-500">Transaction: {{ $commande->paiement->transaction_id }}</p>
                        @endif
                    @else
                        <p class="text-gray-500">Paiement non effectué</p>
                    @endif
                </div>
                
                {{-- Livraison --}}
                @if($commande->livraison)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-2">🚚 Livraison</h3>
                    <p class="text-gray-800">{{ $commande->livraison->adresse_livraison }}</p>
                    @if($commande->livraison->instructions)
                        <p class="text-sm text-gray-500 mt-1">Instructions: {{ $commande->livraison->instructions }}</p>
                    @endif
                </div>
                @endif
            </div>
            
            {{-- Actions --}}
            <div class="px-6 py-6 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-3">
                @if($commande->statut === 'livree' && !$commande->avis)
                    <button wire:click="openReviewModal"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                        ⭐ Laisser un avis
                    </button>
                @endif
                
                @if(in_array($commande->statut, ['en_attente', 'confirmee']))
                    <button wire:click="cancelOrder"
                            wire:confirm="Êtes-vous sûr de vouloir annuler cette commande ?"
                            class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                        Annuler la commande
                    </button>
                @endif
                
                <a href="{{ route('client.restaurants.show', $commande->restaurant->id) }}"
                   class="bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700 transition">
                    Revoir le restaurant
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Temps réel pour les mises à jour de statut
    window.Echo?.private(`commande.{{ $commande->id }}`)
        .listen('.commande-statut-updated', (data) => {
            Livewire.dispatch('refresh');
            
            if (Notification.permission === 'granted') {
                new Notification('Mise à jour de votre commande', {
                    body: data.message,
                    icon: '/favicon.ico'
                });
            }
        });
</script>
@endpush
@endsection
