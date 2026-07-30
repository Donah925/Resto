@props(['notifications' => [], 'unreadCount' => 0])

<div x-data="{ 
    open: false, 
    notifications: @js($notifications),
    unreadCount: {{ $unreadCount }}
}" 
     @click.away="open = false"
     class="relative">
    
    {{-- Bouton de notification --}}
    <button @click="open = !open" 
            class="relative p-2 text-gray-600 hover:text-orange-600 transition-colors">
        <span class="text-2xl">🔔</span>
        
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    
    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
         class="absolute right-0 mt-2 w-80 md:w-96 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
         style="display: none;">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-orange-500 to-red-600 px-4 py-3">
            <div class="flex items-center justify-between text-white">
                <h3 class="font-bold text-lg">Notifications</h3>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" 
                            class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded-full transition">
                        Tout marquer comme lu
                    </button>
                @endif
            </div>
        </div>
        
        {{-- Liste des notifications --}}
        <div class="max-h-96 overflow-y-auto">
            @if(count($notifications) === 0)
                <div class="p-8 text-center text-gray-500">
                    <span class="text-4xl mb-2 block">🔕</span>
                    <p>Aucune notification</p>
                </div>
            @else
                @foreach($notifications as $notification)
                    <div class="border-b border-gray-100 hover:bg-gray-50 transition-colors {{ isset($notification['non_lu']) && $notification['non_lu'] ? 'bg-orange-50/50' : '' }}">
                        <a href="{{ $notification['url'] ?? '#' }}" 
                           class="block p-4"
                           @if(isset($notification['non_lu']) && $notification['non_lu'])
                               wire:click="markAsRead({{ $notification['id'] }})"
                           @endif>
                            <div class="flex items-start gap-3">
                                {{-- Icône selon le type --}}
                                <span class="text-2xl flex-shrink-0">
                                    @switch($notification['type'] ?? 'info')
                                        @case('commande')
                                            📦
                                            @break
                                        @case('livraison')
                                            🚚
                                            @break
                                        @case('promotion')
                                            🎁
                                            @break
                                        @case('support')
                                            💬
                                            @break
                                        @default
                                            ℹ️
                                    @endswitch
                                </span>
                                
                                {{-- Contenu --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800 font-medium line-clamp-2">
                                        {{ $notification['message'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $notification['created_at']->diffForHumans() }}
                                    </p>
                                    
                                    @if(isset($notification['non_lu']) && $notification['non_lu'])
                                        <span class="inline-block mt-2 w-2 h-2 bg-orange-500 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
        
        {{-- Footer --}}
        <div class="bg-gray-50 px-4 py-3 text-center">
            <a href="{{ route('notifications.index') ?? '#' }}" 
               class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                Voir toutes les notifications →
            </a>
        </div>
    </div>
</div>
