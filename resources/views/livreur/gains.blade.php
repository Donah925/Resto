@extends('layouts.livreur')
@section('title', 'Gains')
@section('page-title', 'Mes Gains')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="text-center mb-6">
        <p class="text-gray-500">Solde disponible</p>
        <p class="text-4xl font-bold text-green-600">{{ number_format(Auth::user()->livreur->solde ?? 0, 2) }} €</p>
    </div>
    <form method="POST" action="{{ route('livreur.gains.retrait') }}" class="mb-6">
        @csrf
        <button class="w-full bg-green-600 text-white px-4 py-2 rounded-lg">Demander un retrait</button>
    </form>
    <h3 class="font-semibold mb-2">Historique</h3>
    <ul class="space-y-2">
        @forelse($gains as $g)
        <li class="flex justify-between"><span>{{ $g->created_at->format('d/m/Y') }}</span><span class="text-green-600">+{{ number_format($g->montant, 2) }} €</span></li>
        @empty
        <li class="text-gray-500">Aucun gain enregistré</li>
        @endforelse
    </ul>
</div>
@endsection
