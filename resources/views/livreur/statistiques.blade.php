@extends('layouts.livreur')
@section('title', 'Statistiques')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Statistiques</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-stats-card title="Livraisons totales" value="{{ $stats['total'] ?? 0 }}" icon="truck" color="indigo"/>
        <x-stats-card title="Cette semaine" value="{{ $stats['week'] ?? 0 }}" icon="calendar" color="green"/>
        <x-stats-card title="Gains totaux" value="{{ number_format($stats['earnings'] ?? 0, 2) }}€" icon="currency-euro" color="yellow"/>
        <x-stats-card title="Note moyenne" value="{{ number_format($stats['rating'] ?? 0, 1) }}/5" icon="star" color="purple"/>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-medium mb-4">Évolution des gains</h3>
        <div class="h-64 flex items-center justify-center text-gray-400">Graphique à implémenter</div>
    </div>
</div>
@endsection
