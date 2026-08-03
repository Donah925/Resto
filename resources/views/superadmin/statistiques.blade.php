@extends('layouts.superadmin')
@section('title', 'Statistiques')
@section('page-title', 'Statistiques Globales')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <x-stats-card title="Utilisateurs" value="{{ $stats['utilisateurs'] ?? 0 }}" icon="users" color="blue" />
    <x-stats-card title="Restaurants" value="{{ $stats['restaurants'] ?? 0 }}" icon="building-storefront" color="orange" />
    <x-stats-card title="Commandes" value="{{ $stats['commandes'] ?? 0 }}" icon="shopping-cart" color="green" />
    <x-stats-card title="Revenu Total" value="{{ number_format($stats['revenu'] ?? 0, 2) }} €" icon="currency-euro" color="purple" />
</div>
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Évolution des commandes</h3>
    <canvas id="commandesChart"></canvas>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('commandesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: { labels: {!! json_encode($labels ?? []) !!}, datasets: [{ label: 'Commandes', data: {!! json_encode($data ?? []) !!}, borderColor: 'rgb(234, 88, 12)', tension: 0.1 }] }
});
</script>
@endpush
