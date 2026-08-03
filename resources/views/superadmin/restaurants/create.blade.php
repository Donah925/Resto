@extends('layouts.superadmin')
@section('title', 'Nouveau restaurant')
@section('header', 'Créer un restaurant')

@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('superadmin.restaurants.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-8 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du restaurant *</label>
                <input type="text" name="nom_fr" value="{{ old('nom_fr') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                @error('nom_fr') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="tel" name="telephone" value="{{ old('telephone') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                <input type="text" name="adresse" value="{{ old('adresse') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                    <input type="text" name="ville" value="{{ old('ville', 'Abidjan') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitude *</label>
                    <input type="number" step="0.000001" name="latitude" value="{{ old('latitude') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitude *</label>
                    <input type="number" step="0.000001" name="longitude" value="{{ old('longitude') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description_fr" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('description_fr') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image de couverture</label>
                    <input type="file" name="image_couverture" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="font-bold text-lg mb-4">Configuration</h3>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="livraison_activee" value="1" checked class="rounded text-orange-600">
                        <span class="ml-2">Livraison activée</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="retrait_active" value="1" checked class="rounded text-orange-600">
                        <span class="ml-2">Retrait sur place activé</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="sur_place_active" value="1" checked class="rounded text-orange-600">
                        <span class="ml-2">Consommation sur place activée</span>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant minimum commande (FCFA)</label>
                        <input type="number" name="montant_minimum_commande" value="{{ old('montant_minimum_commande', 3000) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rayon max livraison (km)</label>
                        <input type="number" name="rayon_max_livraison" value="{{ old('rayon_max_livraison', 10) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('superadmin.restaurants.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Créer le restaurant
                </button>
            </div>
        </form>
    </div>
@endsection
