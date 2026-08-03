@extends('layouts.client')
@section('title', 'Détails de la réservation')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('client.reservations.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Réservation #{{ $reservation->id }}</h1>
    </div>
    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <div class="flex justify-between items-center">
            <x-badge-statut :status="$reservation->status"/>
            <span class="text-gray-500">{{ $reservation->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="border-t pt-4">
            <div class="flex items-center space-x-4">
                <img src="{{ $reservation->restaurant->image_url ?? 'https://via.placeholder.com/80' }}" class="w-20 h-20 rounded-lg object-cover">
                <div>
                    <h3 class="font-semibold">{{ $reservation->restaurant->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $reservation->restaurant->address }}</p>
                </div>
            </div>
        </div>
        <div class="border-t pt-4 grid grid-cols-2 gap-4">
            <div><dt class="text-sm text-gray-500">Date</dt><dd class="font-medium">{{ $reservation->date->format('d/m/Y') }}</dd></div>
            <div><dt class="text-sm text-gray-500">Heure</dt><dd class="font-medium">{{ $reservation->time }}</dd></div>
            <div><dt class="text-sm text-gray-500">Couverts</dt><dd class="font-medium">{{ $reservation->guests }} personnes</dd></div>
            <div><dt class="text-sm text-gray-500">Téléphone</dt><dd class="font-medium">{{ $reservation->user->phone ?? 'N/A' }}</dd></div>
        </div>
        @if($reservation->notes)
        <div class="border-t pt-4">
            <dt class="text-sm text-gray-500">Notes</dt>
            <dd class="text-gray-700">{{ $reservation->notes }}</dd>
        </div>
        @endif
        <div class="flex justify-end space-x-4 pt-4 border-t">
            @if($reservation->status == 'pending')
            <form action="{{ route('client.reservations.cancel', $reservation) }}" method="POST" onsubmit="return confirm('Annuler cette réservation ?')">
                @csrf @method('DELETE')
                <button class="btn-danger">Annuler</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
