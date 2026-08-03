@extends('layouts.livreur')
@section('title', 'Disponibilité')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold">Disponibilité</h1>
    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-medium">Statut actuel</h3>
                <p class="text-sm text-gray-500">Recevoir des propositions de livraison</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" {{ auth()->user()->is_available ? 'checked' : '' }} class="sr-only peer" name="is_available">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>
        <div class="border-t pt-6">
            <h3 class="font-medium mb-4">Horaires habituels</h3>
            <div class="space-y-3">
                @foreach(['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'] as $day)
                <div class="flex items-center justify-between">
                    <span class="text-sm">{{ $day }}</span>
                    <div class="flex space-x-2">
                        <input type="time" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="text-gray-400">-</span>
                        <input type="time" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="flex justify-end pt-4 border-t">
            <button class="btn-primary">Enregistrer</button>
        </div>
    </div>
</div>
@endsection
