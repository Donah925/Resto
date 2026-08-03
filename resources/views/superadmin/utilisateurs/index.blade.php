@extends('layouts.superadmin')
@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Liste des utilisateurs</h2>
        <a href="{{ route('superadmin.utilisateurs.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">Nouvel utilisateur</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($utilisateurs as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->nom }} {{ $user->prenom }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap"><x-badge-statut :value="$user->role" /></td>
                    <td class="px-6 py-4 whitespace-nowrap"><x-badge-statut :value="$user->actif ? 'Actif' : 'Inactif'" :type="$user->actif ? 'success' : 'danger'" /></td>
                    <td class="px-6 py-4 whitespace-nowrap space-x-2">
                        <a href="{{ route('superadmin.utilisateurs.show', $user) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                        <a href="{{ route('superadmin.utilisateurs.edit', $user) }}" class="text-green-600 hover:text-green-900">Modifier</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun utilisateur trouvé</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">{{ $utilisateurs->links() }}</div>
</div>
@endsection
