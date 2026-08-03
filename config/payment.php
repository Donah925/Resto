<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    */
    'default' => env('PAYMENT_DEFAULT', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Available Payment Gateways
    |--------------------------------------------------------------------------
    */
    'gateways' => [
        'stripe',
        'paypal',
        'mtn_momo',
        'moov_money',
        'orange_money',
        'wave',
        'payeer',
        'payoneer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PayPal Configuration
    |--------------------------------------------------------------------------
    */
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'environment' => env('PAYPAL_ENVIRONMENT', 'sandbox'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | MTN Mobile Money Configuration
    |--------------------------------------------------------------------------
    */
    'mtn_momo' => [
        'base_url' => env('MTN_MOMO_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
        'api_key' => env('MTN_MOMO_API_KEY'),
        'user_id' => env('MTN_MOMO_USER_ID'),
        'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'environment' => env('MTN_MOMO_ENVIRONMENT', 'sandbox'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Moov Money Configuration
    |--------------------------------------------------------------------------
    */
    'moov_money' => [
        'base_url' => env('MOOV_MONEY_BASE_URL'),
        'api_key' => env('MOOV_MONEY_API_KEY'),
        'merchant_id' => env('MOOV_MONEY_MERCHANT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Orange Money Configuration
    |--------------------------------------------------------------------------
    */
    'orange_money' => [
        'base_url' => env('ORANGE_MONEY_BASE_URL'),
        'client_id' => env('ORANGE_MONEY_CLIENT_ID'),
        'client_secret' => env('ORANGE_MONEY_CLIENT_SECRET'),
        'merchant_key' => env('ORANGE_MONEY_MERCHANT_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Wave Configuration
    |--------------------------------------------------------------------------
    */
    'wave' => [
        'base_url' => env('WAVE_BASE_URL'),
        'api_key' => env('WAVE_API_KEY'),
        'merchant_id' => env('WAVE_MERCHANT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payeer Configuration
    |--------------------------------------------------------------------------
    */
    'payeer' => [
        'merchant_id' => env('PAYEER_MERCHANT_ID'),
        'secret_key' => env('PAYEER_SECRET_KEY'),
        'base_url' => env('PAYEER_BASE_URL', 'https://payeer.com/merchant'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payoneer Configuration
    |--------------------------------------------------------------------------
    */
    'payoneer' => [
        'api_url' => env('PAYONEER_API_URL'),
        'api_user' => env('PAYONEER_API_USER'),
        'api_key' => env('PAYONEER_API_KEY'),
    ],

];
