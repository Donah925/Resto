@extends('layouts.gerant')
@section('title', 'Réservations')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Réservations</h1>
        <a href="#" class="btn-primary"><x-heroicon-o-calendar class="w-5 h-5 mr-2"/>Vue calendrier</a>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex space-x-4">
            <input type="date" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Tous statuts</option>
                <option value="pending">En attente</option>
                <option value="confirmed">Confirmée</option>
                <option value="cancelled">Annulée</option>
            </select>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Heure</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Couverts</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reservations ?? [] as $res)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $res->user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $res->date->format('d/m/Y') }} à {{ $res->time }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $res->guests }} pers.</td>
                    <td class="px-6 py-4 whitespace-nowrap"><x-badge-statut :status="$res->status"/></td>
                    <td class="px-6 py-4 whitespace-nowrap space-x-2">
                        <a href="{{ route('gerant.reservations.show', $res) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center"><x-empty-state icon="calendar" title="Aucune réservation" message="Aucune réservation pour le moment."/></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
