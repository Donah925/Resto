@extends('layouts.superadmin')
@section('title', 'Restaurants')
@section('header', 'Gestion des restaurants')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600">{{ $restaurants->total() }} restaurants au total</p>
        </div>
        <a href="{{ route('superadmin.restaurants.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
            + Nouveau restaurant
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Restaurant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ville</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($restaurants as $restaurant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="{{ $restaurant->logo ? asset('storage/' . $restaurant->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($restaurant->nom) }}"
                                     class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <p class="font-semibold">{{ $restaurant->nom }}</p>
                                    <p class="text-xs text-gray-500">{{ $restaurant->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $restaurant->ville }}</td>
                        <td class="px-6 py-4">
                            <span class="text-yellow-500">⭐</span>
                            <span class="font-semibold">{{ number_format($restaurant->note, 1) }}</span>
                            <span class="text-xs text-gray-500">({{ $restaurant->total_avis }})</span>
                        </td>
                        <td class="px-6 py-4">
                            <x-badge-statut :statut="$restaurant->statut" />
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('superadmin.restaurants.show', $restaurant) }}" class="text-blue-600 hover:underline">Voir</a>
                            <a href="{{ route('superadmin.restaurants.edit', $restaurant) }}" class="text-orange-600 hover:underline">Modifier</a>
                            <form action="{{ route('superadmin.restaurants.destroy', $restaurant) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Supprimer ce restaurant ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-empty-state icon="🏪" title="Aucun restaurant" description="Commencez par créer votre premier restaurant" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $restaurants->links() }}
    </div>
@endsection
