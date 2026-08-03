@extends('layouts.admin')
@section('title', 'Détails du restaurant')
@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.restaurants.index') }}" class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $restaurant->name }}</h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 space-y-6">
            <div class="flex items-start space-x-4">
                <img src="{{ $restaurant->image_url ?? 'https://via.placeholder.com/150' }}" class="w-24 h-24 rounded-lg object-cover">
                <div>
                    <h2 class="text-xl font-semibold">{{ $restaurant->name }}</h2>
                    <p class="text-gray-500">{{ $restaurant->cuisine_type }}</p>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $restaurant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
            <div class="border-t pt-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><dt class="text-sm text-gray-500">Email</dt><dd class="font-medium">{{ $restaurant->email }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Téléphone</dt><dd class="font-medium">{{ $restaurant->phone ?? 'N/A' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Adresse</dt><dd class="font-medium">{{ $restaurant->address }}, {{ $restaurant->city }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Note</dt><dd class="font-medium flex items-center"><x-heroicon-s-star class="w-4 h-4 text-yellow-400 mr-1"/>{{ number_format($restaurant->average_rating ?? 0, 1) }}/5</dd></div>
                </dl>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <h3 class="font-medium">Actions</h3>
            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="btn-primary w-full justify-center">Modifier</a>
            @if(!$restaurant->is_active)
            <form action="{{ route('admin.restaurants.activate', $restaurant) }}" method="POST">@csrf<button class="btn-success w-full justify-center">Activer</button></form>
            @else
            <form action="{{ route('admin.restaurants.deactivate', $restaurant) }}" method="POST">@csrf<button class="btn-warning w-full justify-center">Désactiver</button></form>
            @endif
        </div>
    </div>
</div>
@endsection
