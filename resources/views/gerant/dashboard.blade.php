@extends('layouts.gerant')
@section('title', 'Dashboard Gérant')
@section('page-title', 'Tableau de bord')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <x-stats-card title="Commandes du jour" value="{{ $stats['commandes_jour'] ?? 0 }}" icon="clipboard-document-list" color="orange" />
    <x-stats-card title="Réservations" value="{{ $stats['reservations'] ?? 0 }}" icon="calendar" color="blue" />
    <x-stats-card title="Revenu jour" value="{{ number_format($stats['revenu_jour'] ?? 0, 2) }} €" icon="currency-euro" color="green" />
    <x-stats-card title="Avis récents" value="{{ $stats['avis'] ?? 0 }}" icon="star" color="yellow" />
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Commandes en cours</h3>
        <ul class="space-y-2">
            @forelse($commandes_en_cours as $cmd)
            <li class="flex justify-between items-center p-2 bg-gray-50 rounded"><span>Cmd #{{ $cmd->id }}</span><x-badge-statut :value="$cmd->statut" /></li>
            @empty
            <li class="text-gray-500">Aucune commande en cours</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Derniers avis</h3>
        <ul class="space-y-2">
            @forelse($avis as $a)
            <li class="flex justify-between items-center p-2 bg-gray-50 rounded"><span>{{ $a->client->nom }}</span><span class="text-yellow-500">{{ str_repeat('★', $a->note) }}</span></li>
            @empty
            <li class="text-gray-500">Aucun avis récent</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
