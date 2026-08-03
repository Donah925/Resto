@extends('layouts.gerant')
@section('title', 'Détail Commande')
@section('page-title', 'Commande #'.$commande->id)
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-xl font-semibold">Commande #{{ $commande->id }}</h2>
            <p class="text-gray-500">{{ $commande->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <x-badge-statut :value="$commande->statut" />
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold mb-2">Client</h3>
            <p>{{ $commande->client->nom }} {{ $commande->client->prenom }}</p>
            <p class="text-gray-500">{{ $commande->client->email }}</p>
        </div>
        <div>
            <h3 class="font-semibold mb-2">Adresse</h3>
            <p>{{ $commande->adresse_livraison }}</p>
        </div>
    </div>
    
    <h3 class="font-semibold mt-6 mb-2">Articles</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left">Produit</th><th class="px-4 py-2 text-left">Qté</th><th class="px-4 py-2 text-right">Prix</th></tr></thead>
        <tbody>
            @foreach($commande->articles as $article)
            <tr><td class="px-4 py-2">{{ $article->produit->nom }}</td><td class="px-4 py-2">{{ $article->quantite }}</td><td class="px-4 py-2 text-right">{{ number_format($article->prix * $article->quantite, 2) }} €</td></tr>
            @endforeach
        </tbody>
        <tfoot><tr><td colspan="2" class="px-4 py-2 font-semibold">Total</td><td class="px-4 py-2 text-right font-semibold">{{ number_format($commande->total, 2) }} €</td></tr></tfoot>
    </table>
    
    <div class="mt-6 flex space-x-4">
        @if($commande->statut === 'en_attente')
        <form method="POST" action="{{ route('gerant.commandes.update-status', $commande) }}"><@csrf><input type="hidden" name="statut" value="en_preparation"><button class="bg-blue-600 text-white px-4 py-2 rounded">Accepter</button></form>
        <form method="POST" action="{{ route('gerant.commandes.update-status', $commande) }}"><@csrf><input type="hidden" name="statut" value="annulee"><button class="bg-red-600 text-white px-4 py-2 rounded">Refuser</button></form>
        @elseif($commande->statut === 'en_preparation')
        <form method="POST" action="{{ route('gerant.commandes.update-status', $commande) }}"><@csrf><input type="hidden" name="statut" value="prete"><button class="bg-green-600 text-white px-4 py-2 rounded">Marquer prête</button></form>
        @endif
    </div>
</div>
@endsection
