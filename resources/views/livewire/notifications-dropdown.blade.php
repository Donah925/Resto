<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-gray-500">
        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" wire:poll.30s></span>
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    </button>
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 max-h-96 overflow-y-auto" style="display: none;">
        @forelse($notifications as $notif)
        <div class="px-4 py-3 border-b hover:bg-gray-50 {{ $notif->unread ? 'bg-blue-50' : '' }}">
            <p class="text-sm">{{ $notif->message }}</p>
            <p class="text-xs text-gray-500">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
        @empty
        <p class="px-4 py-3 text-sm text-gray-500">Aucune notification</p>
        @endforelse
    </div>
</div>
