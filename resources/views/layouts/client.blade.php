<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Resto') }} - Client</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Mobile Header -->
        <header class="md:hidden bg-white shadow-sm p-4 flex justify-between items-center">
            <a href="{{ route('client.dashboard') }}" class="text-xl font-bold text-orange-600">Resto</a>
            <button x-data="{ open: false }" @click="open = !open" class="text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </header>
        
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white shadow-lg md:block hidden">
            <div class="p-6">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-orange-600">Resto</a>
                <span class="ml-2 text-sm text-gray-500">Client</span>
            </div>
            <nav class="mt-6">
                <x-sidebar-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')" icon="home">Tableau de bord</x-sidebar-link>
                <x-sidebar-link :href="route('client.panier.index')" :active="request()->routeIs('client.panier.*')" icon="shopping-cart">Panier</x-sidebar-link>
                <x-sidebar-link :href="route('client.commandes.index')" :active="request()->routeIs('client.commandes.*')" icon="clipboard-document-list">Commandes</x-sidebar-link>
                <x-sidebar-link :href="route('client.reservations.index')" :active="request()->routeIs('client.reservations.*')" icon="calendar">Réservations</x-sidebar-link>
                <x-sidebar-link :href="route('client.adresses.index')" :active="request()->routeIs('client.adresses.*')" icon="map-pin">Adresses</x-sidebar-link>
                <x-sidebar-link :href="route('client.favoris.index')" :active="request()->routeIs('client.favoris.*')" icon="heart">Favoris</x-sidebar-link>
                <x-sidebar-link :href="route('client.portefeuille')" :active="request()->routeIs('client.portefeuille')" icon="wallet">Portefeuille</x-sidebar-link>
            </nav>
        </aside>
        
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm hidden md:block">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    <div class="flex items-center space-x-4">
                        <livewire:notifications-dropdown />
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2">
                                <div class="w-10 h-10 rounded-full bg-orange-600 flex items-center justify-center text-white font-semibold">{{ substr(Auth::user()->nom, 0, 1) }}{{ substr(Auth::user()->prenom, 0, 1) }}</div>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Déconnexion</button></form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @if(session('success'))<x-alert type="success" message="{{ session('success') }}" />@endif
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
