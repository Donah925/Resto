<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Livreur') - RestoApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-orange-500">🍽️ RestoApp</h1>
                <p class="text-gray-400 text-sm mt-1">Livreur</p>
            </div>

            <nav class="mt-6">
                <x-sidebar-link href="{{ route('livreur.dashboard') }}" icon="📊" :active="request()->routeIs('livreur.dashboard')">
                    Tableau de bord
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Livraisons</p>
                <x-sidebar-link href="{{ route('livreur.livraisons.index') }}" icon="📦" :active="request()->routeIs('livreur.livraisons.*')">
                    Mes livraisons
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('livreur.livraisons.disponibles') }}" icon="🔍" :active="request()->routeIs('livreur.livraisons.disponibles')">
                    Missions disponibles
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Planning</p>
                <x-sidebar-link href="{{ route('livreur.planning') }}" icon="📅" :active="request()->routeIs('livreur.planning')">
                    Planning
                </x-sidebar-link>

                <p class="px-6 pt-4 pb-2 text-xs uppercase text-gray-500">Gains</p>
                <x-sidebar-link href="{{ route('livreur.gains') }}" icon="💰" :active="request()->routeIs('livreur.gains')">
                    Gains
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
                        @livewire('shared.notifications-dropdown')

                        <div class="flex items-center space-x-3">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                            <div>
                                <p class="font-semibold text-gray-800">{{ auth()->user()->nom_complet }}</p>
                                <p class="text-xs text-gray-500">Livreur</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600" title="Déconnexion">🚪</button>
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
