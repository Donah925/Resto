@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Tableau de bord')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <x-stats-card title="Restaurants" value="{{ $stats['restaurants'] ?? 0 }}" icon="building-storefront" color="blue" />
    <x-stats-card title="Gérants" value="{{ $stats['gerants'] ?? 0 }}" icon="users" color="green" />
    <x-stats-card title="Commandes" value="{{ $stats['commandes'] ?? 0 }}" icon="clipboard-document-list" color="orange" />
    <x-stats-card title="Livraisons" value="{{ $stats['livraisons'] ?? 0 }}" icon="truck" color="purple" />
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Derniers restaurants</h3>
        <ul class="space-y-2">
            @forelse($restaurants as $resto)
            <li class="flex justify-between items-center"><span>{{ $resto->nom }}</span><x-badge-statut :value="$resto->statut" /></li>
            @empty
            <li class="text-gray-500">Aucun restaurant</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Dernières commandes</h3>
        <ul class="space-y-2">
            @forelse($commandes as $cmd)
            <li class="flex justify-between items-center"><span>Cmd #{{ $cmd->id }}</span><x-badge-statut :value="$cmd->statut" /></li>
            @empty
            <li class="text-gray-500">Aucune commande</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
