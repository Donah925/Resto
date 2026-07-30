@extends('layouts.app')
@section('title', 'Accueil')

@section('content')
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-orange-500 to-red-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Commandez vos plats préférés en un clic</h1>
            <p class="text-xl mb-8 opacity-90">Découvrez les meilleurs restaurants d'Abidjan et faites-vous livrer</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('client.restaurants.index') }}" class="bg-white text-orange-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Voir les restaurants
                </a>
                <a href="{{ route('register') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-orange-600 transition">
                    Créer un compte
                </a>
            </div>
        </div>
    </section>

    {{-- Restaurants en avant --}}
    <section class="py-16 max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">🔥 Restaurants populaires</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($restaurantsEnAvant ?? [] as $restaurant)
                <x-card-restaurant :restaurant="$restaurant" />
            @empty
                <p class="col-span-3 text-center text-gray-500">Aucun restaurant disponible pour le moment.</p>
            @endforelse
        </div>
    </section>

    {{-- Fonctionnalités --}}
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Pourquoi choisir RestoApp ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-6xl mb-4">🚀</div>
                    <h3 class="text-xl font-bold mb-2">Livraison rapide</h3>
                    <p class="text-gray-600">Recevez vos plats en moins de 30 minutes</p>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4">💳</div>
                    <h3 class="text-xl font-bold mb-2">Paiement sécurisé</h3>
                    <p class="text-gray-600">Mobile Money, CB, portefeuille, cartes cadeaux</p>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4">🎁</div>
                    <h3 class="text-xl font-bold mb-2">Programme fidélité</h3>
                    <p class="text-gray-600">Gagnez des points à chaque commande</p>
                </div>
            </div>
        </div>
    </section>
@endsection
