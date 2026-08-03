@extends('layouts.livreur')
@section('title', 'Dashboard Livreur')
@section('page-title', 'Tableau de bord')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <x-stats-card title="Livraisons jour" value="{{ $stats['livraisons_jour'] ?? 0 }}" icon="truck" color="green" />
    <x-stats-card title="Gains jour" value="{{ number_format($stats['gains_jour'] ?? 0, 2) }} €" icon="currency-euro" color="blue" />
    <x-stats-card title="En cours" value="{{ $stats['en_cours'] ?? 0 }}" icon="clock" color="orange" />
    <x-stats-card title="Évaluation" value="{{ number_format($stats['evaluation'] ?? 0, 1) }}/5" icon="star" color="yellow" />
</div>
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Livraisons en cours</h3>
    @forelse($livraisons_en_cours as $liv)
    <div class="border rounded-lg p-4 mb-4 flex justify-between items-center">
        <div>
            <p class="font-semibold">Cmd #{{ $liv->commande->id }}</p>
            <p class="text-sm text-gray-500">{{ $liv->commande->adresse_livraison }}</p>
        </div>
        <a href="{{ route('livreur.livraisons.show', $liv) }}" class="bg-green-600 text-white px-4 py-2 rounded">Voir</a>
    </div>
    @empty
    <x-empty-state message="Aucune livraison en cours" />
    @endforelse
</div>
@endsection
