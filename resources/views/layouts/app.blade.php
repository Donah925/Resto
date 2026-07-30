<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Header public --}}
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-orange-600">
                        🍽️ RestoApp
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('client.restaurants.index') }}" class="text-gray-700 hover:text-orange-600">
                        Restaurants
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                            Mon espace
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-orange-600">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                            Inscription
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- Contenu principal --}}
    <main class="flex-1">
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} RestoApp. Tous droits réservés.</p>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
