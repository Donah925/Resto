@extends('layouts.gerant')
@section('title', 'Ajouter un produit')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('gerant.menu.index') }}" class="text-gray-500"><x-heroicon-o-arrow-left class="w-6 h-6"/></a>
        <h1 class="text-2xl font-bold">Ajouter un produit</h1>
    </div>
    <form action="{{ route('gerant.produits.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input name="name" label="Nom du produit" required/>
            <x-form-input name="price" type="number" step="0.01" label="Prix (€)" required/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Catégorie</label>
            <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($categories ?? [] as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Image</label>
            <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div><label class="flex items-center"><input type="checkbox" name="is_available" value="1" checked class="rounded border-gray-300 text-indigo-600"><span class="ml-2 text-sm text-gray-700">Disponible</span></label></div>
            <div><label class="flex items-center"><input type="checkbox" name="is_vegetarian" value="1" class="rounded border-gray-300 text-indigo-600"><span class="ml-2 text-sm text-gray-700">Végétarien</span></label></div>
            <div><label class="flex items-center"><input type="checkbox" name="is_spicy" value="1" class="rounded border-gray-300 text-indigo-600"><span class="ml-2 text-sm text-gray-700">Épicé</span></label></div>
        </div>
        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="{{ route('gerant.menu.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Ajouter</button>
        </div>
    </form>
</div>
@endsection
