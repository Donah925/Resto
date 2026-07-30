@props(['statut', 'label' => null])

@php
    $colors = [
        'en_attente' => 'bg-yellow-100 text-yellow-800',
        'confirmee' => 'bg-blue-100 text-blue-800',
        'en_preparation' => 'bg-indigo-100 text-indigo-800',
        'prete' => 'bg-cyan-100 text-cyan-800',
        'en_livraison' => 'bg-purple-100 text-purple-800',
        'livree' => 'bg-green-100 text-green-800',
        'terminee' => 'bg-gray-100 text-gray-800',
        'annulee' => 'bg-red-100 text-red-800',
        'remboursee' => 'bg-orange-100 text-orange-800',
        'actif' => 'bg-green-100 text-green-800',
        'inactif' => 'bg-gray-100 text-gray-800',
        'suspendu' => 'bg-red-100 text-red-800',
    ][$statut] ?? 'bg-gray-100 text-gray-800';

    $labels = [
        'en_attente' => 'En attente',
        'confirmee' => 'Confirmée',
        'en_preparation' => 'En préparation',
        'prete' => 'Prête',
        'en_livraison' => 'En livraison',
        'livree' => 'Livrée',
        'terminee' => 'Terminée',
        'annulee' => 'Annulée',
        'remboursee' => 'Remboursée',
        'actif' => 'Actif',
        'inactif' => 'Inactif',
        'suspendu' => 'Suspendu',
    ][$statut] ?? $statut;
@endphp

<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $colors }}">
    {{ $label ?? $labels }}
</span>
