<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Client') - RestoApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    {{-- Header client --}}
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-orange-600">🍽️ RestoApp</a>
                </div>

                <div class="flex items-center space-x-6">
                    <a href="{{ route('client.restaurants.index') }}" class="text-gray-700 hover:text-orange-600">Restaurants</a>
                    <a href="{{ route('client.commandes.index') }}" class="text-gray-700 hover:text-orange-600">Commandes</a>
                    <a href="{{ route('client.reservations.index') }}" class="text-gray-700 hover:text-orange-600">Réservations</a>
                    
                    @auth
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('client.panier.show') }}" class="relative text-gray-700 hover:text-orange-600">
                                🛒
                                @if(session('panier_count', 0) > 0)
                                    <span class="absolute -top-2 -right-2 bg-orange-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ session('panier_count') }}</span>
                                @endif
                            </a>
                            
                            @livewire('shared.notifications-dropdown')
                            
                            <div class="flex items-center space-x-2 dropdown relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2">
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full" alt="Avatar">
                                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->prenom }}</span>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                    <a href="{{ route('client.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tableau de bord</a>
                                    <a href="{{ route('client.profil.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                                    <a href="{{ route('client.adresses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mes adresses</a>
                                    <a href="{{ route('client.favoris.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Favoris</a>
                                    <a href="{{ route('client.portefeuille.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Portefeuille</a>
                                    <a href="{{ route('client.fidelite.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fidélité</a>
                                    <hr class="my-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Déconnexion</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-orange-600">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">Inscription</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- Contenu principal --}}
    <main class="min-h-screen">
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        @if($errors->any())
            <x-alert type="error" :message="$errors->first()" />
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-orange-500 mb-4">🍽️ RestoApp</h3>
                    <p class="text-sm text-gray-400">La plateforme de restauration nouvelle génération en Côte d'Ivoire.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Liens utiles</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('client.restaurants.index') }}" class="text-gray-400 hover:text-white">Restaurants</a></li>
                        <li><a href="{{ route('client.commandes.index') }}" class="text-gray-400 hover:text-white">Commandes</a></li>
                        <li><a href="{{ route('client.reservations.index') }}" class="text-gray-400 hover:text-white">Réservations</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('client.tickets.index') }}" class="text-gray-400 hover:text-white">Centre d'aide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Moyens de paiement</h4>
                    <div class="flex space-x-2 text-2xl">
                        <span>💳</span><span>📱</span><span>💵</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} RestoApp. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
