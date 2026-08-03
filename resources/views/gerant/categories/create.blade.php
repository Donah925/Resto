@extends('layouts.gerant')
@section('title', 'Nouvelle catégorie')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('gerant.menu.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Nouvelle catégorie</h1>
    </div>
    <form action="{{ route('gerant.categories.store') }}" method="POST" class="space-y-6">
        @csrf
        <x-form-input name="name" label="Nom de la catégorie" required/>
        <x-form-input name="description" label="Description"/>
        <div>
            <label class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
            <input type="number" name="sort_order" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="{{ route('gerant.menu.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Créer</button>
        </div>
    </form>
</div>
@endsection
