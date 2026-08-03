@extends('layouts.superadmin')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('superadmin.utilisateurs.index') }}" class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier l'utilisateur</h1>
    </div>

    <form action="{{ route('superadmin.utilisateurs.update', $user) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="name" label="Nom complet" :value="$user->name" required />
            <x-form-input name="email" type="email" label="Email" :value="$user->email" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="password" type="password" label="Nouveau mot de passe (laisser vide pour ne pas changer)" />
            <x-form-input name="password_confirmation" type="password" label="Confirmer mot de passe" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Rôle</label>
            <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="client" {{ $user->role == 'client' ? 'selected' : '' }}>Client</option>
                <option value="gerant" {{ $user->role == 'gerant' ? 'selected' : '' }}>Gérant</option>
                <option value="livreur" {{ $user->role == 'livreur' ? 'selected' : '' }}>Livreur</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div>
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Utilisateur actif</span>
            </label>
        </div>

        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="{{ route('superadmin.utilisateurs.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
