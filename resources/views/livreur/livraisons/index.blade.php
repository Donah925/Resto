@extends('layouts.livreur')
@section('title', 'Livraisons')
@section('page-title', 'Historique des Livraisons')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="space-y-4">
        @forelse($livraisons as $liv)
        <div class="border rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold">Cmd #{{ $liv->commande->id }} - {{ $liv->commande->client->nom }}</p>
                <p class="text-sm text-gray-500">{{ $liv->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <x-badge-statut :value="$liv->statut" />
                <a href="{{ route('livreur.livraisons.show', $liv) }}" class="text-blue-600">Voir</a>
            </div>
        </div>
        @empty
        <x-empty-state message="Aucune livraison" />
        @endforelse
    </div>
    <div class="mt-4">{{ $livraisons->links() }}</div>
</div>
@endsection
