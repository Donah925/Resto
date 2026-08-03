# 📦 Installation des SDKs pour les APIs Tierces

## Prérequis

Avant d'installer les SDKs, assurez-vous d'avoir:
- ✅ PHP 8.2 ou supérieur
- ✅ Composer installé
- ✅ Un projet Laravel fonctionnel

## Méthode 1: Script automatique (Recommandé)

Exécutez le script d'installation inclus dans le projet:

```bash
chmod +x install-sdks.sh
./install-sdks.sh
```

Ce script installera automatiquement tous les packages nécessaires.

## Méthode 2: Installation manuelle package par package

Si vous préférez installer les packages individuellement:

### 1. Paiement - Stripe
```bash
composer require stripe/stripe-php
composer require laravel/cashier
```

### 2. Paiement - PayPal
```bash
composer require paypal/paypal-server-sdk
```

### 3. SMS - Twilio
```bash
composer require twilio/sdk
```

### 4. Email - SendGrid
```bash
composer require sendgrid/sendgrid
```

### 5. Google APIs (Maps, OAuth, Analytics)
```bash
composer require google/apiclient:^2.0
```

### 6. Firebase JWT
```bash
composer require firebase/php-jwt
```

### 7. OAuth 2.0 Client (Facebook, Apple, etc.)
```bash
composer require league/oauth2-client
```

### 8. Mapbox
```bash
composer require mapbox/mapbox-sdk-php
```

### 9. Laravel Socialite (Authentification sociale)
```bash
composer require laravel/socialite
```

## Publication des configurations

Après l'installation, publiez les configurations:

```bash
# Pour Laravel Cashier (Stripe)
php artisan vendor:publish --provider="Laravel\Cashier\CashierServiceProvider" --tag="migrations"

# Pour Laravel Socialite
php artisan vendor:publish --provider="Laravel\Socialite\SocialiteServiceProvider" --tag="config"

# Exécutez les migrations
php artisan migrate
```

## Configuration des clés API

Ajoutez les clés suivantes dans votre fichier `.env`:

```env
# Stripe
STRIPE_KEY=your-stripe-publishable-key
STRIPE_SECRET=your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret

# PayPal
PAYPAL_CLIENT_ID=your-paypal-client-id
PAYPAL_SECRET=your-paypal-secret
PAYPAL_MODE=sandbox # ou 'live'

# Twilio
TWILIO_SID=your-twilio-account-sid
TWILIO_TOKEN=your-twilio-auth-token
TWILIO_FROM=your-twilio-phone-number

# SendGrid
SENDGRID_API_KEY=your-sendgrid-api-key
MAIL_FROM_ADDRESS=noreply@votreapp.com
MAIL_FROM_NAME="${APP_NAME}"

# Google
GOOGLE_MAPS_API_KEY=your-google-maps-api-key
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://votreapp.com/auth/google/callback

# Facebook
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URI=https://votreapp.com/auth/facebook/callback

# Apple
APPLE_CLIENT_ID=your-apple-service-id
APPLE_TEAM_ID=your-apple-team-id
APPLE_KEY_ID=your-apple-key-id
APPLE_PRIVATE_KEY_PATH=/path/to/AuthKey.p8
APPLE_REDIRECT_URI=https://votreapp.com/auth/apple/callback

# Firebase
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n..."
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-xxxxx@your-project.iam.gserviceaccount.com

# Mapbox
MAPBOX_ACCESS_TOKEN=your-mapbox-access-token
```

## Vérification de l'installation

Pour vérifier que les SDKs sont correctement installés:

```bash
composer show stripe/stripe-php
composer show paypal/paypal-server-sdk
composer show twilio/sdk
composer show sendgrid/sendgrid
composer show google/apiclient
composer show firebase/php-jwt
composer show league/oauth2-client
composer show mapbox/mapbox-sdk-php
composer show laravel/cashier
composer show laravel/socialite
```

## Test des services

Vous pouvez tester chaque service avec les commandes Artisan suivantes:

```bash
# Tester la connexion Stripe
php artisan tinker
>>> app(\App\Services\Payment\StripeService::class)->verifyConnection()

# Tester l'envoi d'email SendGrid
php artisan tinker
>>> app(\App\Services\Notification\SendGridService::class)->sendTestEmail('votre@email.com')

# Tester l'envoi SMS Twilio
php artisan tinker
>>> app(\App\Services\Notification\TwilioService::class)->sendTestSMS('+33600000000')
```

## Configuration des Webhooks

Pour tester les webhooks en local:

### Stripe CLI
```bash
# Installer Stripe CLI
# macOS: brew install stripe/stripe-cli/stripe
# Linux: wget -O stripe.tar.gz https://github.com/stripe/stripe-cli/releases/latest/download/stripe_1.20.0_linux_x86_64.tar.gz && tar zxvf stripe.tar.gz

# Se connecter
stripe login

# Écouter les événements webhook
stripe listen --forward-to localhost:8000/api/webhooks/stripe
```

### Ngrok (alternative)
```bash
# Installer ngrok
# Démarrer le tunnel
ngrok http 8000

# Configurer l'URL webhook dans le dashboard Stripe/PayPal
https://votre-url-ngrok.io/api/webhooks/stripe
```

## Files d'attente (Queues)

Les webhooks et notifications utilisent les files d'attente. Configurez-les:

```bash
# Dans .env
QUEUE_CONNECTION=database

# Créer la table des jobs
php artisan queue:table
php artisan migrate

# Lancer le worker
php artisan queue:work --tries=3
```

## Prochaines étapes

1. ✅ Installer les SDKs (fait)
2. ✅ Configurer les clés API dans `.env`
3. ✅ Exécuter les migrations
4. ⏳ Configurer les contrôleurs webhook
5. ⏳ Tester les intégrations
6. ⏳ Mettre en production

## Documentation officielle

- [Stripe PHP](https://stripe.com/docs/api?lang=php)
- [PayPal SDK](https://developer.paypal.com/docs/api/overview/)
- [Twilio PHP](https://www.twilio.com/docs/libraries/php)
- [SendGrid PHP](https://docs.sendgrid.com/for-developers/sending-email/php)
- [Google API Client](https://developers.google.com/api-client-library/php)
- [Laravel Cashier](https://laravel.com/docs/billing)
- [Laravel Socialite](https://laravel.com/docs/social-authentication)

---

**Note:** En production, utilisez toujours les clés API "live" et non les clés "sandbox/test".
