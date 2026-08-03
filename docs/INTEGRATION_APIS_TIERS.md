# Intégration des APIs Tiers - Documentation

Ce document décrit l'architecture d'intégration des services externes pour l'application Resto.

## Services Integres

### 1. Paiement
- Stripe : Cartes bancaires
- PayPal : Paiement alternatif

### 2. Communication
- Twilio : SMS (notifications, OTP)
- SendGrid : Emails transactionnels

### 3. Cartographie
- Mapbox : Geocodage, calcul de distance, itineraires

## Architecture

### Structure des fichiers

app/Services/ThirdParty/
- StripeService.php
- PayPalService.php
- TwilioService.php
- SendGridService.php
- MapboxService.php

app/Http/Controllers/Webhooks/
- PaymentWebhookController.php
- TwilioWebhookController.php
- SendGridWebhookController.php

app/Jobs/ThirdParty/
- ProcessStripePayment.php
- SendOrderSmsNotification.php
- SendOrderConfirmationEmail.php
- SendVerificationSms.php

config/services.php (configuration des APIs)
routes/webhooks.php (routes des webhooks)

## Configuration

Toutes les cles API doivent etre definies dans le fichier .env. Le fichier .env.example a ete mis a jour avec tous les parametres necessaires.

Chaque service peut etre active/desactive via une variable *_ENABLED dans le .env.

## Webhooks URLs

- Stripe: /webhooks/stripe
- PayPal: /webhooks/paypal
- Twilio SMS Status: /webhooks/twilio/sms/status
- Twilio SMS Received: /webhooks/twilio/sms/received
- SendGrid: /webhooks/sendgrid

## Packages a installer

composer require stripe/stripe-php
composer require paypal/paypal-checkout-sdk
composer require twilio/sdk
composer require sendgrid/sendgrid

## Prochaines etapes

1. Installer les SDKs necessaires
2. Configurer les comptes chez chaque provider
3. Ajouter les cles API dans .env
4. Configurer les webhooks sur les plateformes externes
5. Tester en environnement de staging
6. Deployer en production
