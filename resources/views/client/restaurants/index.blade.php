@extends('layouts.app')
@section('title', 'Nos Restaurants')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- En-tête --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">🍽️ Nos Restaurants</h1>
            <p class="text-gray-600">Découvrez les meilleurs restaurants d'Abidjan</p>
        </div>
        
        {{-- Filtres et recherche --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Recherche --}}
                <div class="md:col-span-2">
                    <x-form-input 
                        name="search" 
                        placeholder="Rechercher un restaurant..." 
                        icon="🔍"
                        wire:model.live.debounce.300ms="search"
                    />
                </div>
                
                {{-- Type de cuisine --}}
                <div>
                    <select wire:model.live="cuisine" 
                            class="w-full pl-4 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        <option value="">Toutes les cuisines</option>
                        <option value="africaine">Africaine</option>
                        <option value="chinoise">Chinoise</option>
                        <option value="italienne">Italienne</option>
                        <option value="fast-food">Fast-food</option>
                        <option value="indienne">Indienne</option>
                        <option value="libanaise">Libanaise</option>
                        <option value="japonaise">Japonaise</option>
                        <option value="française">Française</option>
                        <option value="senegalaise">Sénégalaise</option>
                        <option value="ivoirienne">Ivoirienne</option>
                    </select>
                </div>
                
                {{-- Tri --}}
                <div>
                    <select wire:model.live="sort" 
                            class="w-full pl-4 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        <option value="popularite">Popularité</option>
                        <option value="note">Meilleure note</option>
                        <option value="livraison_rapide">Livraison rapide</option>
                        <option value="prix_croissant">Prix croissant</option>
                        <option value="prix_decroissant">Prix décroissant</option>
                    </select>
                </div>
            </div>
            
            {{-- Filtres avancés --}}
            <div class="mt-4 flex flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="ouvertSeulement" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm text-gray-700">Ouverts maintenant</span>
                </label>
                
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="livraisonGratuite" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm text-gray-700">Livraison gratuite</span>
                </label>
            </div>
        </div>
        
        {{-- Résultats --}}
        @if($restaurants->count() === 0)
            <x-empty-state 
                icon="🍽️"
                title="Aucun restaurant trouvé"
                message="Essayez de modifier vos filtres ou votre recherche"
            />
        @else
            {{-- Grid des restaurants --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($restaurants as $restaurant)
                    <x-card-restaurant :restaurant="$restaurant" />
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="mt-8">
                {{ $restaurants->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Temps réel pour les statuts d'ouverture
    window.Echo?.channel('restaurants').listen('.restaurant-statut-updated', (data) => {
        Livewire.dispatch('refresh');
    });
</script>
@endpush
@endsection
