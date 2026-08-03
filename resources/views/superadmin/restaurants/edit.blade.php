@extends('layouts.superadmin')
@section('title', 'Modifier le restaurant')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('superadmin.restaurants.index') }}" class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier le restaurant</h1>
    </div>
    <form action="{{ route('superadmin.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="name" label="Nom du restaurant" :value="$restaurant->name" required />
            <x-form-input name="email" type="email" label="Email" :value="$restaurant->email" required />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="phone" label="Téléphone" :value="$restaurant->phone" />
            <x-form-input name="cuisine_type" label="Type de cuisine" :value="$restaurant->cuisine_type" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Adresse</label>
            <input type="text" name="address" value="{{ $restaurant->address }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-form-input name="city" label="Ville" :value="$restaurant->city" required />
            <x-form-input name="postal_code" label="Code postal" :value="$restaurant->postal_code" required />
            <x-form-input name="delivery_fee" type="number" step="0.01" label="Frais de livraison (€)" :value="$restaurant->delivery_fee" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $restaurant->description }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Image</label>
            <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            @if($restaurant->image_url)
            <img src="{{ $restaurant->image_url }}" alt="{{ $restaurant->name }}" class="mt-2 h-32 rounded-lg object-cover">
            @endif
        </div>
        <div class="flex justify-between items-center pt-4 border-t">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $restaurant->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Restaurant actif</span>
            </label>
            <div class="space-x-4">
                <a href="{{ route('superadmin.restaurants.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
