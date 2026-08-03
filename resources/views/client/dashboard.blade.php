@extends('layouts.client')
@section('title', 'Tableau de bord')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Bonjour, {{ auth()->user()->name }}!</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-stats-card title="Commandes" value="{{ $stats['orders'] ?? 0 }}" icon="shopping-bag" color="indigo"/>
        <x-stats-card title="Dépensé" value="{{ number_format($stats['spent'] ?? 0, 2) }}€" icon="currency-euro" color="green"/>
        <x-stats-card title="Favoris" value="{{ $stats['favorites'] ?? 0 }}" icon="heart" color="red"/>
        <x-stats-card title="Points fidélité" value="{{ $stats['points'] ?? 0 }}" icon="star" color="yellow"/>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-medium">Dernières commandes</h3>
                <a href="{{ route('client.commandes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Voir tout</a>
            </div>
            <div class="space-y-4">
                @forelse($recentOrders ?? [] as $order)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $order->restaurant->image_url ?? 'https://via.placeholder.com/50' }}" class="w-12 h-12 rounded object-cover">
                        <div>
                            <p class="font-medium">{{ $order->restaurant->name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <span class="font-semibold">{{ number_format($order->total, 2) }}€</span>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Aucune commande récente</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-medium">Restaurants favoris</h3>
                <a href="{{ route('client.favoris.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Voir tout</a>
            </div>
            <div class="space-y-4">
                @forelse($favoriteRestaurants ?? [] as $rest)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $rest->image_url ?? 'https://via.placeholder.com/50' }}" class="w-12 h-12 rounded object-cover">
                        <div>
                            <p class="font-medium">{{ $rest->name }}</p>
                            <p class="text-sm text-gray-500">{{ $rest->cuisine_type }}</p>
                        </div>
                    </div>
                    <a href="{{ route('client.restaurants.show', $rest) }}" class="text-indigo-600 text-sm">Commander</a>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Aucun favori</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
