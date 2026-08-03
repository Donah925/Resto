@extends('layouts.client')
@section('title', 'Mes Adresses')
@section('page-title', 'Mes Adresses de Livraison')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Mes adresses</h2>
        <a href="{{ route('client.adresses.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg">Nouvelle adresse</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($adresses as $addr)
        <div class="border rounded-lg p-4 relative">
            @if($addr->par_defaut)<span class="absolute top-2 right-2 bg-green-600 text-white text-xs px-2 py-1 rounded">Par défaut</span>@endif
            <p class="font-semibold">{{ $addr->label }}</p>
            <p class="text-gray-500">{{ $addr->adresse_complete }}</p>
            <div class="mt-2 flex space-x-2">
                <a href="{{ route('client.adresses.edit', $addr) }}" class="text-blue-600 text-sm">Modifier</a>
                <form method="POST" action="{{ route('client.adresses.destroy', $addr) }}" class="inline">@csrf @method('DELETE')<button class="text-red-600 text-sm">Supprimer</button></form>
            </div>
        </div>
        @empty
        <x-empty-state message="Aucune adresse enregistrée" />
        @endforelse
    </div>
</div>
@endsection
