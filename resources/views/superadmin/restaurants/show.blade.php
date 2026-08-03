@extends('layouts.superadmin')

@section('title', 'Détails du restaurant')

@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('superadmin.restaurants.index') }}" class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Détails du restaurant</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 space-y-6">
            <div class="flex items-start space-x-4">
                <img src="{{ $restaurant->image_url ?? 'https://via.placeholder.com/150' }}" alt="{{ $restaurant->name }}" class="w-24 h-24 rounded-lg object-cover">
                <div>
                    <h2 class="text-xl font-semibold">{{ $restaurant->name }}</h2>
                    <p class="text-gray-500">{{ $restaurant->cuisine_type ?? 'Non spécifié' }}</p>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $restaurant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>

            <div class="border-t pt-4">
                <h3 class="text-lg font-medium mb-4">Informations</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $restaurant->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $restaurant->phone ?? 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Adresse</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $restaurant->address }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Ville</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $restaurant->city }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Note moyenne</dt>
                        <dd class="mt-1 text-sm font-medium flex items-center">
                            <x-heroicon-s-star class="w-4 h-4 text-yellow-400 mr-1" />
                            {{ number_format($restaurant->average_rating ?? 0, 1) }}/5 ({{ $restaurant->reviews_count ?? 0 }} avis)
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Frais de livraison</dt>
                        <dd class="mt-1 text-sm font-medium">{{ number_format($restaurant->delivery_fee ?? 0, 2) }} €</dd>
                    </div>
                </dl>
            </div>

            @if($restaurant->description)
            <div class="border-t pt-4">
                <h3 class="text-lg font-medium mb-2">Description</h3>
                <p class="text-sm text-gray-600">{{ $restaurant->description }}</p>
            </div>
            @endif
        </div>

        <!-- Actions et Gérant -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h3 class="text-lg font-medium">Actions</h3>
                <a href="{{ route('superadmin.restaurants.edit', $restaurant) }}" class="btn-primary w-full justify-center">
                    <x-heroicon-o-pencil class="w-5 h-5 mr-2" />
                    Modifier
                </a>
                @if(!$restaurant->is_active)
                <form action="{{ route('superadmin.restaurants.activate', $restaurant) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-success w-full justify-center">
                        <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                        Activer
                    </button>
                </form>
                @else
                <form action="{{ route('superadmin.restaurants.deactivate', $restaurant) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-warning w-full justify-center">
                        <x-heroicon-o-pause-circle class="w-5 h-5 mr-2" />
                        Désactiver
                    </button>
                </form>
                @endif
                <form action="{{ route('superadmin.restaurants.destroy', $restaurant) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger w-full justify-center">
                        <x-heroicon-o-trash class="w-5 h-5 mr-2" />
                        Supprimer
                    </button>
                </form>
            </div>

            @if($restaurant->user)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium mb-4">Gérant</h3>
                <div class="flex items-center space-x-3">
                    <img src="{{ $restaurant->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($restaurant->user->name) }}" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="font-medium">{{ $restaurant->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $restaurant->user->email }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
