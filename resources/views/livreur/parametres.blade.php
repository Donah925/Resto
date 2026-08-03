@extends('layouts.livreur')
@section('title', 'Paramètres')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold">Paramètres</h1>
    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <h3 class="font-medium">Informations personnelles</h3>
        <form action="#" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input name="name" label="Nom" :value="auth()->user()->name" required/>
                <x-form-input name="email" type="email" label="Email" :value="auth()->user()->email" required/>
            </div>
            <x-form-input name="phone" label="Téléphone" :value="auth()->user()->phone"/>
            <button type="submit" class="btn-primary">Mettre à jour</button>
        </form>
        <div class="border-t pt-6">
            <h3 class="font-medium text-red-600">Zone dangereuse</h3>
            <p class="text-sm text-gray-500 mb-4">Supprimer votre compte de manière permanente.</p>
            <form action="#" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                @csrf @method('DELETE')
                <button class="btn-danger">Supprimer mon compte</button>
            </form>
        </div>
    </div>
</div>
@endsection
