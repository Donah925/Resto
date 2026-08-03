@extends('layouts.app')
@section('title', 'Mes Commandes')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- En-tête --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">📦 Mes Commandes</h1>
            <p class="text-gray-600">Suivez toutes vos commandes en temps réel</p>
        </div>
        
        {{-- Filtres --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap gap-3">
                <button wire:click="setFilter('all')"
                        class="px-4 py-2 rounded-full transition {{ $filter === 'all' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Toutes
                </button>
                <button wire:click="setFilter('en_cours')"
                        class="px-4 py-2 rounded-full transition {{ $filter === 'en_cours' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    En cours
                </button>
                <button wire:click="setFilter('terminee')"
                        class="px-4 py-2 rounded-full transition {{ $filter === 'terminee' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Terminées
                </button>
                <button wire:click="setFilter('annulee')"
                        class="px-4 py-2 rounded-full transition {{ $filter === 'annulee' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Annulées
                </button>
            </div>
        </div>
        
        {{-- Liste des commandes --}}
        @if($commandes->count() === 0)
            <x-empty-state 
                icon="📦"
                title="Aucune commande"
                message="Vous n'avez pas encore passé de commande"
                actionText="Découvrir les restaurants"
                actionUrl="{{ route('client.restaurants.index') }}"
            />
        @else
            <div class="space-y-4">
                @foreach($commandes as $commande)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        {{-- Header de la commande --}}
                        <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4 text-white">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div>
                                    <p class="font-bold text-lg">Commande #{{ $commande->reference }}</p>
                                    <p class="text-sm opacity-90">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <x-badge-statut :statut="$commande->statut" type="commande" />
                            </div>
                        </div>
                        
                        {{-- Corps de la commande --}}
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Détails restaurant --}}
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-2">🏠 Restaurant</h3>
                                    <p class="text-gray-600">{{ $commande->restaurant->nom }}</p>
                                </div>
                                
                                {{-- Total --}}
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-2">💰 Total</h3>
                                    <p class="text-2xl font-bold text-orange-600">{{ number_format($commande->montant_total, 0) }} FCFA</p>
                                    @if($commande->paiement)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Payé via {{ $commande->paiement->methode }}
                                        </p>
                                    @endif
                                </div>
                                
                                {{-- Livraison --}}
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-2">🚚 Livraison</h3>
                                    @if($commande->livraison)
                                        <p class="text-gray-600 text-sm">
                                            {{ $commande->livraison->adresse_livraison ?? 'Adresse non renseignée' }}
                                        </p>
                                        @if($commande->livraison->livreur)
                                            <p class="text-sm text-orange-600 mt-1">
                                                Livreur: {{ $commande->livraison->livreur->utilisateur->nom }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-gray-500 text-sm">À emporter</p>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Articles --}}
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-3">📝 Articles commandés</h3>
                                <div class="space-y-2">
                                    @foreach($commande->lignes as $ligne)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">
                                                {{ $ligne->quantite }}x {{ $ligne->produit->nom }}
                                                @if($ligne->options)
                                                    <span class="text-xs text-gray-500">({{ implode(', ', $ligne->options) }})</span>
                                                @endif
                                            </span>
                                            <span class="text-gray-600">{{ number_format($ligne->prix_unitaire * $ligne->quantite, 0) }} FCFA</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Actions --}}
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('client.commandes.show', $commande->id) }}" 
                                   class="bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-700 transition text-sm">
                                    Voir les détails
                                </a>
                                
                                @if($commande->statut === 'livree' && !$commande->avis)
                                    <button wire:click="openReviewModal({{ $commande->id }})"
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition text-sm">
                                        ⭐ Laisser un avis
                                    </button>
                                @endif
                                
                                @if(in_array($commande->statut, ['en_attente', 'confirmee']))
                                    <button wire:click="cancelOrder({{ $commande->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir annuler cette commande ?"
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition text-sm">
                                        Annuler la commande
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Suivi en temps réel --}}
                        @if(in_array($commande->statut, ['en_cours', 'prete', 'livraison_en_cours']))
                            <div class="bg-orange-50 px-6 py-4 border-t border-orange-100">
                                <div class="flex items-center gap-2 text-orange-700">
                                    <span class="animate-pulse">🔴</span>
                                    <span class="text-sm font-medium">Suivi en temps réel disponible</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="mt-8">
                {{ $commandes->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal d'avis --}}
<div wire:ignore.self 
     x-data="{ open: false }"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50"></div>
        
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Laisser un avis</h3>
            
            {{-- Formulaire d'avis --}}
            <form wire:submit="submitReview">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    wire:click="setRating({{ $i }})"
                                    class="text-3xl {{ $i <= $rating ? 'text-yellow-500' : 'text-gray-300' }}">
                                ★
                            </button>
                        @endfor
                    </div>
                </div>
                
                <x-form-input name="commentaire" 
                              label="Commentaire" 
                              type="textarea"
                              placeholder="Partagez votre expérience..."
                              required />
                
                <div class="flex gap-3 mt-6">
                    <button type="button" 
                            @click="open = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition">
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Temps réel pour le suivi des commandes
    window.Echo?.private(`commande.{{ auth()->id() }}`)
        .listen('.commande-statut-updated', (data) => {
            Livewire.dispatch('refresh');
            
            // Notification
            if (Notification.permission === 'granted') {
                new Notification('Votre commande a été mise à jour', {
                    body: data.message,
                    icon: '/favicon.ico'
                });
            }
        });
</script>
@endpush
@endsection
