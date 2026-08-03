@extends('layouts.app')
@section('title', 'Mon Profil')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- En-tête --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">👤 Mon Profil</h1>
            <p class="text-gray-600">Gérez vos informations personnelles</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Sidebar navigation --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-orange-400 to-red-500 mx-auto flex items-center justify-center text-white text-3xl font-bold mb-3">
                            {{ substr(auth()->user()->nom, 0, 1) }}
                        </div>
                        <h3 class="font-bold text-gray-800">{{ auth()->user()->nom }}</h3>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <nav class="space-y-2">
                        <button wire:click="setTab('profile')" 
                                class="w-full text-left px-4 py-2 rounded-lg transition {{ $tab === 'profile' ? 'bg-orange-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            📝 Informations personnelles
                        </button>
                        <button wire:click="setTab('addresses')" 
                                class="w-full text-left px-4 py-2 rounded-lg transition {{ $tab === 'addresses' ? 'bg-orange-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            📍 Mes adresses
                        </button>
                        <button wire:click="setTab('security')" 
                                class="w-full text-left px-4 py-2 rounded-lg transition {{ $tab === 'security' ? 'bg-orange-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            🔐 Sécurité
                        </button>
                        <button wire:click="setTab('payment')" 
                                class="w-full text-left px-4 py-2 rounded-lg transition {{ $tab === 'payment' ? 'bg-orange-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            💳 Moyens de paiement
                        </button>
                        <button wire:click="setTab('favorites')" 
                                class="w-full text-left px-4 py-2 rounded-lg transition {{ $tab === 'favorites' ? 'bg-orange-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            ❤️ Favoris
                        </button>
                    </nav>
                </div>
            </div>
            
            {{-- Contenu principal --}}
            <div class="md:col-span-2">
                @if($tab === 'profile')
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Informations personnelles</h2>
                        
                        <form wire:submit="updateProfile">
                            <x-form-input name="nom" 
                                          label="Nom complet" 
                                          wire:model="nom"
                                          required
                                          icon="👤" />
                            
                            <x-form-input name="email" 
                                          label="Email" 
                                          type="email"
                                          wire:model="email"
                                          required
                                          icon="📧" />
                            
                            <x-form-input name="telephone" 
                                          label="Téléphone" 
                                          type="tel"
                                          wire:model="telephone"
                                          placeholder="+225 XX XX XX XX XX"
                                          icon="📱" />
                            
                            <x-form-input name="date_naissance" 
                                          label="Date de naissance" 
                                          type="date"
                                          wire:model="date_naissance"
                                          icon="🎂" />
                            
                            <div class="mt-6">
                                <button type="submit" 
                                        class="bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700 transition">
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                @elseif($tab === 'addresses')
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Mes adresses</h2>
                            <button wire:click="openAddressModal" 
                                    class="bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-700 transition text-sm">
                                + Nouvelle adresse
                            </button>
                        </div>
                        
                        @if($adresses->count() === 0)
                            <x-empty-state 
                                icon="📍"
                                title="Aucune adresse"
                                message="Ajoutez votre première adresse de livraison"
                            />
                        @else
                            <div class="space-y-4">
                                @foreach($adresses as $adresse)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-orange-300 transition">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="text-xl">🏠</span>
                                                    <h3 class="font-semibold text-gray-800">{{ $adresse->libelle }}</h3>
                                                    @if($adresse->est_defaut)
                                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">Défaut</span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-600 text-sm">
                                                    {{ $adresse->quartier }}, {{ $adresse->ville }}<br>
                                                    {{ $adresse->description ?? '' }}
                                                </p>
                                                @if($adresse->latitude && $adresse->longitude)
                                                    <p class="text-xs text-gray-500 mt-2 font-mono">
                                                        📍 {{ number_format($adresse->latitude, 6) }}, {{ number_format($adresse->longitude, 6) }}
                                                    </p>
                                                @endif
                                            </div>
                                            
                                            <div class="flex gap-2">
                                                <button wire:click="editAddress({{ $adresse->id }})" 
                                                        class="text-blue-600 hover:text-blue-800 p-2">
                                                    ✏️
                                                </button>
                                                @if(!$adresse->est_defaut)
                                                    <button wire:click="setDefaultAddress({{ $adresse->id }})" 
                                                            class="text-green-600 hover:text-green-800 p-2"
                                                            title="Définir comme défaut">
                                                        ⭐
                                                    </button>
                                                @endif
                                                <button wire:click="deleteAddress({{ $adresse->id }})" 
                                                        wire:confirm="Êtes-vous sûr ?"
                                                        class="text-red-600 hover:text-red-800 p-2">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @elseif($tab === 'security')
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Sécurité</h2>
                        
                        <form wire:submit="changePassword">
                            <x-form-input name="current_password" 
                                          label="Mot de passe actuel" 
                                          type="password"
                                          wire:model="current_password"
                                          required
                                          icon="🔒" />
                            
                            <x-form-input name="new_password" 
                                          label="Nouveau mot de passe" 
                                          type="password"
                                          wire:model="new_password"
                                          required
                                          icon="🔑" />
                            
                            <x-form-input name="new_password_confirmation" 
                                          label="Confirmer le nouveau mot de passe" 
                                          type="password"
                                          wire:model="new_password_confirmation"
                                          required
                                          icon="🔑" />
                            
                            <div class="mt-6">
                                <button type="submit" 
                                        class="bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700 transition">
                                    Changer le mot de passe
                                </button>
                            </div>
                        </form>
                        
                        {{-- Sessions actives --}}
                        <div class="mt-8 pt-8 border-t">
                            <h3 class="font-bold text-gray-800 mb-4">Sessions actives</h3>
                            <div class="space-y-3">
                                @foreach($sessions as $session)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl">💻</span>
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $session->device }}</p>
                                                <p class="text-xs text-gray-500">{{ $session->ip_address }} - Dernière activité: {{ $session->last_active }}</p>
                                            </div>
                                        </div>
                                        @if(!$session->is_current)
                                            <button wire:click="logoutSession('{{ $session->id }}')" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                Déconnecter
                                            </button>
                                        @else
                                            <span class="text-green-600 text-sm font-medium">Actuelle</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($tab === 'payment')
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Moyens de paiement</h2>
                            <button class="bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-700 transition text-sm">
                                + Ajouter
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            {{-- Mobile Money --}}
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-2xl">
                                        📱
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800">Orange Money / MTN MoMo</h3>
                                        <p class="text-sm text-gray-500">Paiement par Mobile Money</p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800">Configurer</button>
                                </div>
                            </div>
                            
                            {{-- Carte bancaire --}}
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">
                                        💳
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800">Carte bancaire</h3>
                                        <p class="text-sm text-gray-500">Visa, Mastercard</p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800">Ajouter</button>
                                </div>
                            </div>
                            
                            {{-- Portefeuille --}}
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">
                                        👛
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800">Portefeuille RestoApp</h3>
                                        <p class="text-sm text-gray-500">Solde: <strong>0 FCFA</strong></p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800">Recharger</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($tab === 'favorites')
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Restaurants favoris</h2>
                        
                        @if($favoris->count() === 0)
                            <x-empty-state 
                                icon="❤️"
                                title="Aucun favori"
                                message="Ajoutez des restaurants à vos favoris"
                                actionText="Découvrir les restaurants"
                                actionUrl="{{ route('client.restaurants.index') }}"
                            />
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($favoris as $restaurant)
                                    <x-card-restaurant :restaurant="$restaurant" size="small" />
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
