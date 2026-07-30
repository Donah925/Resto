@extends('layouts.superadmin')
@section('title', 'Tableau de bord')
@section('header', 'Tableau de bord SuperAdmin')

@section('content')
    {{-- Stats globales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card title="Restaurants" :value="$stats['restaurants']" icon="🏪" color="blue" :trend="$stats['restaurants_trend']" />
        <x-stats-card title="Utilisateurs" :value="$stats['utilisateurs']" icon="👥" color="green" :trend="$stats['utilisateurs_trend']" />
        <x-stats-card title="Commandes (jour)" :value="$stats['commandes_jour']" icon="📋" color="orange" :trend="$stats['commandes_trend']" />
        <x-stats-card title="CA (jour)" :value="number_format($stats['ca_jour'], 0, ',', ' ') . ' FCFA'" icon="💰" color="purple" :trend="$stats['ca_trend']" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Restaurants récents --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Restaurants récents</h3>
                <a href="{{ route('superadmin.restaurants.index') }}" class="text-orange-600 hover:underline text-sm">Voir tout →</a>
            </div>
            <div class="space-y-3">
                @forelse($restaurantsRecents as $restaurant)
                    <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <img src="{{ $restaurant->logo ? asset('storage/' . $restaurant->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($restaurant->nom) }}"
                                 class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <p class="font-semibold">{{ $restaurant->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $restaurant->ville }}</p>
                            </div>
                        </div>
                        <x-badge-statut :statut="$restaurant->statut" />
                    </div>
                @empty
                    <x-empty-state icon="🏪" title="Aucun restaurant" />
                @endforelse
            </div>
        </div>

        {{-- Activité récente --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold mb-4">Activité récente</h3>
            <div class="space-y-3">
                @forelse($activitesRecentes as $activite)
                    <div class="flex items-start p-3 border-l-4 border-orange-500 bg-orange-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm"><strong>{{ $activite->utilisateur?->nom_complet ?? 'Système' }}</strong></p>
                            <p class="text-xs text-gray-600">{{ $activite->action }} sur {{ $activite->entite_type }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activite->cree_le->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="📭" title="Aucune activité" />
                @endforelse
            </div>
        </div>
    </div>
@endsection
