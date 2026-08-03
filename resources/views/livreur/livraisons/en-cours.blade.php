@extends('layouts.livreur')
@section('title', 'Livraisons en cours')
@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('livreur.livraisons.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Livraisons en cours</h1>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($livraisons ?? [] as $livraison)
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <div class="flex justify-between items-start">
                <h3 class="font-semibold">Livraison #{{ $livraison->id }}</h3>
                <x-badge-statut :status="$livraison->status"/>
            </div>
            <div class="space-y-2 text-sm">
                <p class="flex items-center"><x-heroicon-o-location-marker class="w-4 h-4 mr-2 text-gray-400"/>{{ $livraison->restaurant->name }}</p>
                <p class="flex items-center"><x-heroicon-o-home class="w-4 h-4 mr-2 text-gray-400"/>{{ Str::limit($livraison->delivery_address, 40) }}</p>
            </div>
            <div class="flex space-x-2 pt-4 border-t">
                <a href="{{ route('livreur.livraisons.show', $livraison) }}" class="btn-secondary flex-1 justify-center">Voir détails</a>
                @if($livraison->status == 'picked_up')
                <form action="{{ route('livreur.livraisons.deliver', $livraison) }}" method="POST">@csrf<button class="btn-success flex-1 justify-center">Livrer</button></form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center">
            <x-empty-state icon="truck" title="Aucune livraison en cours" message="Vous n'avez pas de livraison en cours pour le moment."/>
        </div>
        @endforelse
    </div>
</div>
@endsection
