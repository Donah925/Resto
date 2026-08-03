@extends('layouts.client')
@section('title', 'Nouvelle réservation')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('client.reservations.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Nouvelle réservation</h1>
    </div>
    <form action="{{ route('client.reservations.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Restaurant</label>
            <select name="restaurant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($restaurants ?? [] as $rest)
                <option value="{{ $rest->id }}">{{ $rest->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="date" type="date" label="Date" :value="date('Y-m-d')" required/>
            <x-form-input name="time" type="time" label="Heure" value="19:00" required/>
        </div>
        <x-form-input name="guests" type="number" min="1" max="20" label="Nombre de personnes" value="2" required/>
        <div>
            <label class="block text-sm font-medium text-gray-700">Notes spéciales</label>
            <textarea name="notes" rows="3" placeholder="Allergies, anniversaire, etc." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>
        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="{{ route('client.reservations.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Réserver</button>
        </div>
    </form>
</div>
@endsection
