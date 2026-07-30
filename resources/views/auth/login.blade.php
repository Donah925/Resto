@extends('layouts.auth')
@section('title', 'Connexion')

@section('content')
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Connexion</h2>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-orange-600">
                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-orange-600 hover:underline">
                Mot de passe oublié ?
            </a>
        </div>

        <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
            Se connecter
        </button>
    </form>

    <p class="text-center text-gray-600 mt-6">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="text-orange-600 font-semibold hover:underline">S'inscrire</a>
    </p>
@endsection
