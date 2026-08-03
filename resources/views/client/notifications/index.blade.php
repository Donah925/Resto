@extends('layouts.client')
@section('title', 'Notifications')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Notifications</h1>
        <button class="text-sm text-indigo-600 hover:text-indigo-900">Tout marquer comme lu</button>
    </div>
    <div class="bg-white rounded-lg shadow divide-y">
        @forelse($notifications ?? [] as $notif)
        <div class="p-4 hover:bg-gray-50 {{ !$notif->read_at ? 'bg-indigo-50' : '' }}">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if($notif->type == 'order_status')
                    <x-heroicon-o-shopping-bag class="w-6 h-6 text-indigo-600"/>
                    @elseif($notif->type == 'promotion')
                    <x-heroicon-o-tag class="w-6 h-6 text-green-600"/>
                    @else
                    <x-heroicon-o-bell class="w-6 h-6 text-gray-600"/>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $notif->data['title'] ?? 'Notification' }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $notif->data['message'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <x-empty-state icon="bell" title="Aucune notification" message="Vous n'avez pas de nouvelles notifications."/>
        </div>
        @endforelse
    </div>
</div>
@endsection
