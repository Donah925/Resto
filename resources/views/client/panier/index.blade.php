@extends('layouts.client')
@section('title', 'Mon Panier')
@section('page-title', 'Mon Panier')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('panier') && count(session('panier')) > 0)
    <div class="space-y-4">
        @foreach(session('panier') as $id => $item)
        <div class="flex justify-between items-center border-b pb-4">
            <div class="flex items-center space-x-4">
                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['nom'] }}" class="w-16 h-16 object-cover rounded">
                <div>
                    <h3 class="font-semibold">{{ $item['nom'] }}</h3>
                    <p class="text-gray-500">{{ number_format($item['prix'], 2) }} € x {{ $item['quantite'] }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="font-semibold">{{ number_format($item['prix'] * $item['quantite'], 2) }} €</span>
                <form method="POST" action="{{ route('client.panier.remove', $id) }}">@csrf<button class="text-red-600 hover:text-red-900">Supprimer</button></form>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6 flex justify-between items-center">
        <span class="text-xl font-semibold">Total: {{ number_format(collect(session('panier'))->sum(fn($i) => $i['prix'] * $i['quantite']), 2) }} €</span>
        <a href="{{ route('client.commandes.create') }}" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700">Commander</a>
    </div>
    @else
    <x-empty-state message="Votre panier est vide" />
    <a href="{{ route('client.restaurants.index') }}" class="mt-4 inline-block bg-orange-600 text-white px-4 py-2 rounded-lg">Voir les restaurants</a>
    @endif
</div>
@endsection
