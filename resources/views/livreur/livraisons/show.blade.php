@extends('layouts.livreur')
@section('title', 'Détails de la livraison')
@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('livreur.livraisons.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Livraison #{{ $livraison->id }}</h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 space-y-6">
            <div class="flex justify-between items-center">
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $livraison->status == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">{{ ucfirst($livraison->status) }}</span>
                <span class="text-gray-500">{{ $livraison->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="border-t pt-4">
                <h3 class="font-medium mb-3">Restaurant</h3>
                <div class="flex items-center space-x-3">
                    <img src="{{ $livraison->restaurant->image_url ?? 'https://via.placeholder.com/50' }}" class="w-12 h-12 rounded object-cover">
                    <div><p class="font-medium">{{ $livraison->restaurant->name }}</p><p class="text-sm text-gray-500">{{ $livraison->restaurant->address }}</p></div>
                </div>
            </div>
            <div class="border-t pt-4">
                <h3 class="font-medium mb-3">Client</h3>
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">{{ substr($livraison->user->name, 0, 2) }}</div>
                    <div><p class="font-medium">{{ $livraison->user->name }}</p><p class="text-sm text-gray-500">{{ $livraison->user->phone ?? 'N/A' }}</p></div>
                </div>
            </div>
            <div class="border-t pt-4">
                <h3 class="font-medium mb-3">Adresse de livraison</h3>
                <p class="text-gray-600">{{ $livraison->delivery_address }}</p>
                <div id="map" class="mt-4 h-64 rounded-lg"></div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h3 class="font-medium">Actions</h3>
                @if($livraison->status == 'pending')
                <form action="{{ route('livreur.livraisons.accept', $livraison) }}" method="POST">@csrf<button class="btn-success w-full justify-center">Accepter</button></form>
                <form action="{{ route('livreur.livraisons.reject', $livraison) }}" method="POST">@csrf<button class="btn-danger w-full justify-center">Refuser</button></form>
                @elseif($livraison->status == 'accepted')
                <form action="{{ route('livreur.livraisons.pickup', $livraison) }}" method="POST">@csrf<button class="btn-primary w-full justify-center">Marquer comme récupéré</button></form>
                @elseif($livraison->status == 'picked_up')
                <form action="{{ route('livreur.livraisons.deliver', $livraison) }}" method="POST">@csrf<button class="btn-success w-full justify-center">Confirmer livraison</button></form>
                @endif
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-medium mb-2">Gain estimé</h3>
                <p class="text-2xl font-bold text-green-600">{{ number_format($livraison->delivery_fee ?? 0, 2) }}€</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser Leaflet map ici
});
</script>
@endpush
@endsection
