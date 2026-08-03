@extends('layouts.gerant')
@section('title', 'Commandes')
@section('page-title', 'Gestion des Commandes')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex space-x-4 mb-6">
        <a href="{{ route('gerant.commandes.index') }}" class="px-4 py-2 rounded {{ request('statut') == '' ? 'bg-orange-600 text-white' : 'bg-gray-200' }}">Toutes</a>
        <a href="{{ route('gerant.commandes.index', ['statut' => 'en_attente']) }}" class="px-4 py-2 rounded {{ request('statut') == 'en_attente' ? 'bg-orange-600 text-white' : 'bg-gray-200' }}">En attente</a>
        <a href="{{ route('gerant.commandes.index', ['statut' => 'en_preparation']) }}" class="px-4 py-2 rounded {{ request('statut') == 'en_preparation' ? 'bg-orange-600 text-white' : 'bg-gray-200' }}">En préparation</a>
        <a href="{{ route('gerant.commandes.index', ['statut' => 'prete']) }}" class="px-4 py-2 rounded {{ request('statut') == 'prete' ? 'bg-orange-600 text-white' : 'bg-gray-200' }}">Prêtes</a>
    </div>
    <div class="space-y-4">
        @forelse($commandes as $cmd)
        <div class="border rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold">Cmd #{{ $cmd->id }} - {{ $cmd->client->nom }}</p>
                <p class="text-sm text-gray-500">{{ $cmd->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <x-badge-statut :value="$cmd->statut" />
                <a href="{{ route('gerant.commandes.show', $cmd) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
            </div>
        </div>
        @empty
        <x-empty-state message="Aucune commande trouvée" />
        @endforelse
    </div>
    <div class="mt-4">{{ $commandes->links() }}</div>
</div>
@endsection
