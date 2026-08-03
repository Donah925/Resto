@extends('layouts.admin')
@section('title', 'Modifier le restaurant')
@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.restaurants.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Modifier le restaurant</h1>
    </div>
    <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="name" label="Nom" :value="$restaurant->name" required/>
            <x-form-input name="email" type="email" label="Email" :value="$restaurant->email" required/>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="phone" label="Téléphone" :value="$restaurant->phone"/>
            <x-form-input name="cuisine_type" label="Type de cuisine" :value="$restaurant->cuisine_type"/>
        </div>
        <x-form-input name="address" label="Adresse" :value="$restaurant->address" required/>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-form-input name="city" label="Ville" :value="$restaurant->city" required/>
            <x-form-input name="postal_code" label="Code postal" :value="$restaurant->postal_code" required/>
            <x-form-input name="delivery_fee" type="number" step="0.01" label="Frais livraison" :value="$restaurant->delivery_fee"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $restaurant->description }}</textarea>
        </div>
        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="{{ route('admin.restaurants.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
