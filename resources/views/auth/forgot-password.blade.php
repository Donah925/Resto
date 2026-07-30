@extends('layouts.auth')
@section('title', 'Mot de passe oublié')

@section('content')
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Mot de passe oublié</h2>
    <p class="text-gray-600 text-center mb-6">Entrez votre email pour recevoir un lien de réinitialisation</p>

    @if(session('status'))
        <div class="bg-green-50 border border-green-500 text-green-800 p-4 rounded-lg mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
            Envoyer le lien
        </button>
    </form>

    <p class="text-center mt-6">
        <a href="{{ route('login') }}" class="text-orange-600 hover:underline">Retour à la connexion</a>
    </p>
@endsection
