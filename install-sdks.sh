#!/bin/bash

# Script d'installation des SDKs pour l'intégration des APIs tierces
# Exécuter ce script dans le dossier racine du projet Laravel

echo "🚀 Installation des SDKs pour les APIs tierces..."

# Vérifier que composer est installé
if ! command -v composer &> /dev/null; then
    echo "❌ Composer n'est pas installé. Veuillez l'installer d'abord."
    echo "   Voir: https://getcomposer.org/download/"
    exit 1
fi

# Vérifier que PHP est disponible
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé ou n'est pas dans le PATH."
    exit 1
fi

echo "✅ PHP et Composer détectés"
php -v
composer --version

echo ""
echo "📦 Installation des packages..."

# Paiement - Stripe
echo "   • Installation de Stripe..."
composer require stripe/stripe-php --no-interaction

# Paiement - PayPal (SDK officiel)
echo "   • Installation de PayPal..."
composer require paypal/paypal-server-sdk --no-interaction

# SMS - Twilio
echo "   • Installation de Twilio..."
composer require twilio/sdk --no-interaction

# Email - SendGrid
echo "   • Installation de SendGrid..."
composer require sendgrid/sendgrid --no-interaction

# Google (Maps, OAuth, Analytics)
echo "   • Installation de Google API Client..."
composer require google/apiclient:^2.0 --no-interaction

# Firebase (JWT pour l'authentification)
echo "   • Installation de Firebase JWT..."
composer require firebase/php-jwt --no-interaction

# OAuth 2.0 Client (pour Facebook, Apple, etc.)
echo "   • Installation de League OAuth2 Client..."
composer require league/oauth2-client --no-interaction

# Mapbox (alternative à Google Maps)
echo "   • Installation de Mapbox SDK..."
composer require mapbox/mapbox-sdk-php --no-interaction

# Laravel Cashier pour Stripe (gestion abonnements)
echo "   • Installation de Laravel Cashier Stripe..."
composer require laravel/cashier --no-interaction

# Laravel Socialite (authentification sociale simplifiée)
echo "   • Installation de Laravel Socialite..."
composer require laravel/socialite --no-interaction

echo ""
echo "✅ Tous les SDKs ont été installés avec succès!"

echo ""
echo "🔧 Publication des configurations..."
php artisan vendor:publish --provider="Laravel\Cashier\CashierServiceProvider" --tag="migrations" --force
php artisan vendor:publish --provider="Laravel\Socialite\SocialiteServiceProvider" --tag="config" --force

echo ""
echo "📝 Prochaines étapes:"
echo "   1. Copiez .env.example vers .env si ce n'est pas déjà fait"
echo "   2. Remplissez les clés API dans votre fichier .env"
echo "   3. Exécutez: php artisan migrate"
echo "   4. Configurez vos files d'attente (queue)"
echo "   5. Testez les webhooks en local avec Stripe CLI ou ngrok"
echo ""
echo "🎉 Installation terminée!"
