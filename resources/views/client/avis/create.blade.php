@extends('layouts.client')
@section('title', 'Donner un avis')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('client.commandes.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Donner un avis</h1>
    </div>
    <form action="{{ route('client.avis.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <div class="flex items-center space-x-4">
            <img src="{{ $order->restaurant->image_url ?? 'https://via.placeholder.com/80' }}" class="w-16 h-16 rounded-lg object-cover">
            <div>
                <h3 class="font-semibold">{{ $order->restaurant->name }}</h3>
                <p class="text-sm text-gray-500">Commande #{{ $order->id }}</p>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Note globale</label>
            <div class="flex space-x-2" x-data="{ rating: 0 }">
                @for($i = 1; $i <= 5; $i++)
                <button type="button" @click="rating = {{ $i }}" :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                    <x-heroicon-s-star class="w-8 h-8"/>
                </button>
                @endfor
                <input type="hidden" name="rating" x-model="rating" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Commentaire</label>
            <textarea name="comment" rows="4" placeholder="Partagez votre expérience..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
        </div>
        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="{{ route('client.commandes.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Publier l'avis</button>
        </div>
    </form>
</div>
@endsection
