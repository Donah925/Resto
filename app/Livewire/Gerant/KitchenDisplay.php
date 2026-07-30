<?php

namespace App\Livewire\Gerant;

use Livewire\Component;
use App\Models\Commande;
use App\Enums\StatutCommande;

class KitchenDisplay extends Component
{
    public $commandesEnCours;

    public function mount()
    {
        $this->chargerCommandes();
    }

    public function chargerCommandes()
    {
        $user = auth()->user();
        if (!$user || !$user->profilGerant) {
            $this->commandesEnCours = collect();
            return;
        }

        $restaurantId = $user->profilGerant->restaurant_id;
        $this->commandesEnCours = Commande::where('restaurant_id', $restaurantId)
            ->whereIn('statut', [StatutCommande::EN_ATTENTE, StatutCommande::CONFIRMEE, StatutCommande::EN_PREPARATION])
            ->with(['lignes.produit', 'table'])
            ->orderBy('cree_le', 'asc')
            ->get();
    }

    // Méthode appelée par le frontend quand un événement WebSocket arrive
    public function onNouvelleCommande($data)
    {
        // On recharge les commandes pour afficher la nouvelle instantanément
        $this->chargerCommandes();
        
        // Optionnel : Jouer un son (géré côté frontend)
        $this->dispatch('play-notification-sound');
    }

    public function render()
    {
        return view('livewire.gerant.kitchen-display');
    }
}
