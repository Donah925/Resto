@extends('layouts.client')
@section('title', 'Portefeuille')
@section('page-title', 'Mon Portefeuille')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-md">
    <div class="text-center mb-6">
        <p class="text-gray-500">Solde actuel</p>
        <p class="text-4xl font-bold text-orange-600">{{ number_format(Auth::user()->portefeuille ?? 0, 2) }} €</p>
    </div>
    <form method="POST" action="{{ route('client.portefeuille.recharger') }}" class="space-y-4">
        @csrf
        <x-form-input name="montant" label="Montant à ajouter" type="number" step="0.01" required />
        <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">Recharger</button>
    </form>
    <h3 class="font-semibold mt-6 mb-2">Historique</h3>
    <ul class="space-y-2">
        @forelse($transactions as $t)
        <li class="flex justify-between"><span>{{ $t->created_at->format('d/m/Y') }}</span><span class="{{ $t->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">{{ $t->type === 'credit' ? '+' : '-' }}{{ number_format($t->montant, 2) }} €</span></li>
        @empty
        <li class="text-gray-500">Aucune transaction</li>
        @endforelse
    </ul>
</div>
@endsection
