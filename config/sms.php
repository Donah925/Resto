<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Sender
    |--------------------------------------------------------------------------
    */
    'default' => env('SMS_DEFAULT', 'twilio'),

    /*
    |--------------------------------------------------------------------------
    | Available SMS Senders
    |--------------------------------------------------------------------------
    */
    'senders' => [
        'twilio',
        'orange_sms',
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
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
    | Orange SMS Configuration
    |--------------------------------------------------------------------------
    */
    'orange' => [
        'base_url' => env('ORANGE_SMS_BASE_URL'),
        'client_id' => env('ORANGE_SMS_CLIENT_ID'),
        'client_secret' => env('ORANGE_SMS_CLIENT_SECRET'),
        'sender_name' => env('ORANGE_SMS_SENDER_NAME', 'OrangeSMS'),
    ],

];
