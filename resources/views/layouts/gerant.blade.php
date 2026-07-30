<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Gérant') - RestoApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-orange-500">🍽️ RestoApp</h1>
                <p class="text-gray-400 text-sm mt-1">Espace Gérant</p>
            </div>

            <nav class="mt-6">
                <x-sidebar-link href="{{ route('gerant.dashboard') }}" icon="📊" :active="request()->routeIs('gerant.dashboard')">
                    Tableau de bord
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Restaurant</p>
                <x-sidebar-link href="{{ route('gerant.restaurant.edit') }}" icon="🏪" :active="request()->routeIs('gerant.restaurant.*')">
                    Mon restaurant
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('gerant.horaires.index') }}" icon="🕐" :active="request()->routeIs('gerant.horaires.*')">
                    Horaires
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('gerant.tables.index') }}" icon="🪑" :active="request()->routeIs('gerant.tables.*')">
                    Tables & Salles
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Menu</p>
                <x-sidebar-link href="{{ route('gerant.categories.index') }}" icon="📂" :active="request()->routeIs('gerant.categories.*')">
                    Catégories
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('gerant.produits.index') }}" icon="🍔" :active="request()->routeIs('gerant.produits.*')">
                    Produits
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('gerant.menus-du-jour.index') }}" icon="📅" :active="request()->routeIs('gerant.menus-du-jour.*')">
                    Menus du jour
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Opérations</p>
                <x-sidebar-link href="{{ route('gerant.commandes.index') }}" icon="📋" :active="request()->routeIs('gerant.commandes.*')">
                    Commandes
                    @php $count = \App\Models\Commande::duRestaurant(auth()->user()->profilGerant->restaurant_id)->enCours()->count(); @endphp
                    @if($count > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $count }}</span>
                    @endif
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('gerant.reservations.index') }}" icon="📆" :active="request()->routeIs('gerant.reservations.*')">
                    Réservations
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('gerant.livraisons.index') }}" icon="🛵" :active="request()->routeIs('gerant.livraisons.*')">
                    Livraisons
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Marketing</p>
                <x-sidebar-link href="{{ route('gerant.avis.index') }}" icon="⭐" :active="request()->routeIs('gerant.avis.*')">
                    Avis clients
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('gerant.codes-promo.index') }}" icon="🎟️" :active="request()->routeIs('gerant.codes-promo.*')">
                    Codes promo
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Analyse</p>
                <x-sidebar-link href="{{ route('gerant.statistiques.index') }}" icon="📈" :active="request()->routeIs('gerant.statistiques.*')">
                    Statistiques
                </x-sidebar-link>
            </nav>
        </aside>

        {{-- Contenu principal --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <h2 class="text-2xl font-bold text-gray-800">@yield('header', 'Tableau de bord')</h2>

                    <div class="flex items-center space-x-4">
                        {{-- Notifications --}}
                        @livewire('shared.notifications-dropdown')

                        {{-- Profil utilisateur --}}
                        <div class="flex items-center space-x-3">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                            <div>
                                <p class="font-semibold text-gray-800">{{ auth()->user()->nom_complet }}</p>
                                <p class="text-xs text-gray-500">Gérant</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600" title="Déconnexion">
                                🚪
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Contenu --}}
            <main class="flex-1 overflow-y-auto p-6">
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
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
