@extends('layouts.admin')
@section('title', 'Restaurants')
@section('page-title', 'Gestion des Restaurants')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Liste des restaurants</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($restaurants as $restaurant)
        <x-card-restaurant :restaurant="$restaurant" />
        @empty
        <p class="col-span-full text-center text-gray-500">Aucun restaurant trouvé</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $restaurants->links() }}</div>
</div>
@endsection
