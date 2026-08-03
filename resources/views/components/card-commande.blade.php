@props(['commande', 'simple' => false])

<div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
    @if(!$simple)
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-lg">Commande #{{ $commande->numero }}</h3>
                    <p class="text-sm text-gray-600">{{ $commande->restaurant->nom }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $commande->cree_le->format('d/m/Y H:i') }}</p>
                </div>
                <x-badge-statut :statut="$commande->statut->value" />
            </div>

            <div class="space-y-2 mb-4">
                @foreach($commande->lignes->take(3) as $ligne)
                    <div class="flex justify-between text-sm">
                        <span>{{ $ligne->quantite }}x {{ $ligne->produit->nom }}</span>
                        <span class="font-medium">{{ number_format($ligne->prix_total_ligne, 0, ',', ' ') }} F</span>
                    </div>
                @endforeach
                @if($commande->lignes->count() > 3)
                    <p class="text-xs text-gray-500">+ {{ $commande->lignes->count() - 3 }} autre(s)</p>
                @endif
            </div>

            <div class="flex justify-between items-center pt-4 border-t">
                <div>
                    <span class="text-sm text-gray-600">Total:</span>
                    <span class="text-xl font-bold text-orange-600 ml-2">{{ number_format($commande->montant_total, 0, ',', ' ') }} F</span>
                </div>
                <a href="{{ route('client.commandes.show', $commande) }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 text-sm">
                    Voir détails →
                </a>
            </div>
        </div>
    @else
        {{-- Version simple pour écrans cuisine / livreur --}}
        <div class="p-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-bold">#{{ $commande->numero }}</h3>
                    <p class="text-xs text-gray-600">{{ $commande->type_commande->label() }}</p>
                </div>
                <x-badge-statut :statut="$commande->statut->value" />
            </div>
            <p class="text-sm text-gray-600 mb-2">{{ $commande->lignes->count() }} article(s)</p>
            <p class="font-bold text-orange-600">{{ number_format($commande->montant_total, 0, ',', ' ') }} F</p>
        </div>
    @endif
</div>
