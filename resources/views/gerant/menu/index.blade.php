@extends('layouts.gerant')
@section('title', 'Gestion du Menu')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Menu</h1>
        <a href="{{ route('gerant.menu.create') }}" class="btn-primary">
            <x-heroicon-o-plus class="w-5 h-5 mr-2"/>Nouveau plat
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <div class="flex space-x-4">
                <input type="text" placeholder="Rechercher un plat..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <select class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Toutes catégories</option>
                    @foreach($categories ?? [] as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
            @forelse($produits ?? [] as $produit)
            <div class="border rounded-lg overflow-hidden hover:shadow-md transition">
                <img src="{{ $produit->image_url ?? 'https://via.placeholder.com/300x200' }}" class="w-full h-40 object-cover">
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <h3 class="font-semibold">{{ $produit->name }}</h3>
                        <span class="text-indigo-600 font-bold">{{ number_format($produit->price, 2) }}€</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ $produit->category->name ?? 'Non catégorisé' }}</p>
                    <div class="mt-3 flex space-x-2">
                        <a href="{{ route('gerant.produits.edit', $produit) }}" class="text-blue-600 hover:text-blue-900 text-sm">Modifier</a>
                        <form action="{{ route('gerant.produits.destroy', $produit) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center">
                <x-empty-state icon="academic-cap" title="Aucun plat" message="Commencez par ajouter un nouveau plat à votre menu."/>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
