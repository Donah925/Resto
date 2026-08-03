<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have\n    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe (Paiement)
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PayPal (Paiement)
    |--------------------------------------------------------------------------
    */
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'environment' => env('PAYPAL_ENVIRONMENT', 'sandbox'), // sandbox ou production
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio (SMS)
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'enabled' => env('TWILIO_ENABLED', false),
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from_number' => env('TWILIO_FROM_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SendGrid (Emails transactionnels)
    |--------------------------------------------------------------------------
    */
    'sendgrid' => [
        'enabled' => env('SENDGRID_ENABLED', false),
        'api_key' => env('SENDGRID_API_KEY'),
        'from_email' => env('SENDGRID_FROM_EMAIL'),
        'from_name' => env('SENDGRID_FROM_NAME', config('app.name')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mapbox (Cartographie et géolocalisation)
    |--------------------------------------------------------------------------
    */
    'mapbox' => [
        'enabled' => env('MAPBOX_ENABLED', false),
        'api_key' => env('MAPBOX_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google (Maps, OAuth, Analytics)
    |--------------------------------------------------------------------------
    */
    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Facebook / Meta (OAuth, Pixel)
    |--------------------------------------------------------------------------
    */
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
        'pixel_id' => env('FACEBOOK_PIXEL_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Apple (OAuth - Sign in with Apple)
    |--------------------------------------------------------------------------
    */
    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI'),
        'team_id' => env('APPLE_TEAM_ID'),
        'key_id' => env('APPLE_KEY_ID'),
        'private_key' => env('APPLE_PRIVATE_KEY_PATH'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase (Analytics, Push Notifications)
    |--------------------------------------------------------------------------
    */
    'firebase' => [
        'enabled' => env('FIREBASE_ENABLED', false),
        'credentials' => env('FIREBASE_CREDENTIALS_PATH'),
        'database_url' => env('FIREBASE_DATABASE_URL'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Brevo (Email transactionnel)
    |--------------------------------------------------------------------------
    */
    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudinary (Storage)
    |--------------------------------------------------------------------------
    */
    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Meilisearch (Search)
    |--------------------------------------------------------------------------
    */
    'meilisearch' => [
        'base_url' => env('MEILISEARCH_BASE_URL', 'http://localhost:7700'),
        'api_key' => env('MEILISEARCH_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | DeepL (Translation)
    |--------------------------------------------------------------------------
    */
    'deepl' => [
        'api_key' => env('DEEPL_API_KEY'),
        'environment' => env('DEEPL_ENVIRONMENT', 'free'), // free ou paid
    ],

];
