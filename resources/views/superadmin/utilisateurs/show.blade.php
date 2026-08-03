@extends('layouts.superadmin')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('superadmin.utilisateurs.index') }}" class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Détails de l'utilisateur</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 space-y-6">
            <div class="flex items-center space-x-4">
                <img src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full">
                <div>
                    <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>

            <div class="border-t pt-4">
                <h3 class="text-lg font-medium mb-4">Informations du compte</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Rôle</dt>
                        <dd class="mt-1 text-sm font-medium capitalize">{{ $user->role }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $user->phone ?? 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Inscrit le</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $user->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Dernière connexion</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $user->last_login_at?->format('d/m/Y à H:i') ?? 'Jamais' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <h3 class="text-lg font-medium">Actions</h3>
            <a href="{{ route('superadmin.utilisateurs.edit', $user) }}" class="btn-primary w-full justify-center">
                <x-heroicon-o-pencil class="w-5 h-5 mr-2" />
                Modifier
            </a>
            @if(!$user->is_active)
            <form action="{{ route('superadmin.utilisateurs.activate', $user) }}" method="POST">
                @csrf
                <button type="submit" class="btn-success w-full justify-center">
                    <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                    Activer
                </button>
            </form>
            @else
            <form action="{{ route('superadmin.utilisateurs.deactivate', $user) }}" method="POST">
                @csrf
                <button type="submit" class="btn-warning w-full justify-center">
                    <x-heroicon-o-pause-circle class="w-5 h-5 mr-2" />
                    Désactiver
                </button>
            </form>
            @endif
            <form action="{{ route('superadmin.utilisateurs.destroy', $user) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger w-full justify-center">
                    <x-heroicon-o-trash class="w-5 h-5 mr-2" />
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Statistiques</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-stats-card title="Commandes" value="{{ $stats['commandes'] ?? 0 }}" icon="shopping-cart" color="indigo" />
            <x-stats-card title="Dépensé" value="{{ number_format($stats['depense'] ?? 0, 2) }} €" icon="currency-euro" color="green" />
            <x-stats-card title="Avis" value="{{ $stats['avis'] ?? 0 }}" icon="star" color="yellow" />
            <x-stats-card title="Signalements" value="{{ $stats['signalements'] ?? 0 }}" icon="flag" color="red" />
        </div>
    </div>
</div>
@endsection
