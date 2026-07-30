<div>
    <h2>👨‍🍳 Écran de Cuisine</h2>
    
    <div class="grid grid-cols-3 gap-4">
        @foreach($commandesEnCours as $commande)
            <div class="border p-4 rounded shadow bg-white" wire:key="{{ $commande->id }}">
                <h3 class="font-bold text-lg">Commande #{{ $commande->numero }}</h3>
                <p class="text-gray-600">Table: {{ $commande->table?->numero_table ?? 'N/A' }}</p>
                <ul class="mt-2">
                    @foreach($commande->lignes as $ligne)
                        <li>- {{ $ligne->quantite }}x {{ $ligne->produit->nom }} 
                            @if($ligne->notes) <span class="text-red-500 text-sm">({{ $ligne->notes }})</span> @endif
                        </li>
                    @endforeach
                </ul>
                <button wire:click="changerStatut('{{ $commande->id }}', 'prete')" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
                    Marquer comme Prête
                </button>
            </div>
        @endforeach
    </div>

    @script
    <script>
        // Écouter l'événement WebSocket
        window.Echo.private(`restaurant.{{ auth()->user()->profilGerant->restaurant_id }}`)
            .listen('.commande.recue', (e) => {
                // Appeler la méthode Livewire 'onNouvelleCommande'
                $wire.onNouvelleCommande(e);
            });
    </script>
    @endscript
</div>
