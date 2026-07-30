# ⚡ Configuration WebSocket & Temps Réel - Projet Resto

## 📋 Résumé des fichiers créés

### Événements (Events)
- `app/Events/NouvelleCommandeRecue.php` - Diffusé quand une nouvelle commande est créée
- `app/Events/StatutCommandeModifie.php` - Diffusé quand le statut d'une commande change
- `app/Events/PositionLivreurMiseAJour.php` - Diffusé pour le suivi GPS en temps réel

### Écouteurs (Listeners)
- `app/Listeners/EnvoyerNotificationNouvelleCommande.php` - Envoie notifications et SMS
- `app/Listeners/MettreAJourStatistiquesVente.php` - Met à jour les stats et l'audit

### Providers & Routes
- `app/Providers/EventServiceProvider.php` - Enregistrement des events/listeners
- `routes/channels.php` - Autorisation des canaux privés WebSocket

### Frontend
- `resources/js/bootstrap.js` - Configuration Echo/Laravel Reverb
- `app/Livewire/Gerant/KitchenDisplay.php` - Composant écran de cuisine
- `resources/views/livewire/gerant/kitchen-display.blade.php` - Vue Blade avec écoute WebSocket

### Configuration
- `.env.example` - Variables d'environnement pour Reverb

---

## 🚀 Commandes d'installation

```bash
# 1. Installer le support broadcasting (Reverb)
php artisan install:broadcasting

# 2. Créer la table des queues
php artisan queue:table
php artisan migrate

# 3. Installer les dépendances JS (si npm est disponible)
npm install laravel-echo pusher-js
npm run build
```

---

## 🔧 Démarrage des services

### Terminal 1 - Serveur WebSocket (Reverb)
```bash
php artisan reverb:start
# Ou avec debug :
php artisan reverb:start --debug
```

### Terminal 2 - Worker des Queues
```bash
php artisan queue:work
# Ou en mode surveillance :
php artisan queue:listen
```

### Terminal 3 - Serveur Laravel
```bash
php artisan serve
```

---

## 📡 Architecture Temps Réel

1. **Action** : Le client valide sa commande
2. **Service** : `OrderService` sauvegarde en BDD
3. **Event** : `event(new NouvelleCommandeRecue($commande))` déclenché
4. **Broadcast** : Laravel envoie à Reverb sur `restaurant.{id}`
5. **Listener** : Notification envoyée via Queue (non-bloquant)
6. **Frontend** : L'écran de cuisine se met à jour instantanément

---

## 🔐 Canaux Privés

| Canal | Accès autorisé |
|-------|----------------|
| `restaurant.{id}` | SuperAdmin, Admin (si accès), Gérant du restaurant |
| `client.{id}` | Client propriétaire du profil |
| `livraison.{id}` | Client de la commande, Livreur assigné, Staff |

---

## 📝 Exemple d'utilisation dans un Service

```php
// Dans OrderService::creerDepuisPanier()
use App\Events\NouvelleCommandeRecue;

// ... après création de la commande
event(new NouvelleCommandeRecue($commande));

// Dans OrderService::changerStatut()
use App\Events\StatutCommandeModifie;

$ancienStatut = $commande->statut;
$commande->update(['statut' => $nouveauStatut]);
event(new StatutCommandeModifie($commande, $ancienStatut, $nouveauStatut));

// Dans DeliveryService::enregistrerPosition()
use App\Events\PositionLivreurMiseAJour;

event(new PositionLivreurMiseAJour($livraison->id, $lat, $lng));
```

---

## 🎯 Prochaines étapes

1. Ajouter les autres événements listés dans la documentation complète
2. Créer les composants Livewire pour le suivi client
3. Intégrer Google Maps/Leaflet pour le suivi GPS
4. Configurer Redis en production pour les queues
5. Ajouter les sons de notification en cuisine
