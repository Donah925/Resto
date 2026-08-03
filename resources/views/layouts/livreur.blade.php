<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Resto') }} - Livreur</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white shadow-lg hidden md:block">
            <div class="p-6">
                <a href="{{ route('livreur.dashboard') }}" class="flex items-center">
                    <span class="text-2xl font-bold text-green-600">Resto</span>
                    <span class="ml-2 text-sm text-gray-500">Livreur</span>
                </a>
            </div>
            <nav class="mt-6">
                <x-sidebar-link :href="route('livreur.dashboard')" :active="request()->routeIs('livreur.dashboard')" icon="home">Tableau de bord</x-sidebar-link>
                <x-sidebar-link :href="route('livreur.livraisons.index')" :active="request()->routeIs('livreur.livraisons.*')" icon="truck">Livraisons</x-sidebar-link>
                <x-sidebar-link :href="route('livreur.planning')" :active="request()->routeIs('livreur.planning')" icon="calendar">Planning</x-sidebar-link>
                <x-sidebar-link :href="route('livreur.gains')" :active="request()->routeIs('livreur.gains')" icon="currency-dollar">Gains</x-sidebar-link>
                <x-sidebar-link :href="route('livreur.vehicule')" :active="request()->routeIs('livreur.vehicule')" icon="motorcycle">Véhicule</x-sidebar-link>
            </nav>
        </aside>
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    <div class="flex items-center space-x-4">
                        <livewire:notifications-dropdown />
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2">
                                <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-semibold">{{ substr(Auth::user()->nom, 0, 1) }}{{ substr(Auth::user()->prenom, 0, 1) }}</div>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Déconnexion</button></form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @if(session('success'))<x-alert type="success" message="{{ session('success') }}" />@endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
