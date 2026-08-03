@extends('layouts.client')
@section('title', 'Mes Réservations')
@section('page-title', 'Mes Réservations')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Historique des réservations</h2>
        <a href="{{ route('client.reservations.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg">Nouvelle réservation</a>
    </div>
    <div class="space-y-4">
        @forelse($reservations as $res)
        <div class="border rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold">{{ $res->restaurant->nom }}</p>
                <p class="text-sm text-gray-500">{{ $res->date_reservation->format('d/m/Y H:i') }} - {{ $res->nombre_personnes }} personnes</p>
            </div>
            <div class="flex items-center space-x-4">
                <x-badge-statut :value="$res->statut" />
                <a href="{{ route('client.reservations.show', $res) }}" class="text-blue-600">Voir</a>
            </div>
        </div>
        @empty
        <x-empty-state message="Aucune réservation" />
        @endforelse
    </div>
</div>
@endsection
