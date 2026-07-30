<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - RestoApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-orange-50 to-orange-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-4xl font-bold text-orange-600">🍽️ RestoApp</a>
            <p class="text-gray-600 mt-2">La plateforme de restauration nouvelle génération</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @yield('content')
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; {{ date('Y') }} RestoApp
        </p>
    </div>
</body>
</html>
