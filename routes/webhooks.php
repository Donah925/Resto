<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhooks\PaymentWebhookController;
use App\Http\Controllers\Webhooks\TwilioWebhookController;
use App\Http\Controllers\Webhooks\SendGridWebhookController;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| Ces routes gèrent les webhooks des services tiers (Stripe, PayPal, Twilio, SendGrid).
| Elles sont généralement appelées par les services externes et ne nécessitent pas
| d'authentification utilisateur (mais peuvent avoir une vérification de signature).
|
*/

// Stripe Webhooks
Route::post('/webhooks/stripe', [PaymentWebhookController::class, 'stripe'])
    ->name('webhooks.stripe');

// PayPal Webhooks
Route::post('/webhooks/paypal', [PaymentWebhookController::class, 'paypal'])
    ->name('webhooks.paypal');

// Twilio Webhooks (SMS status & received)
Route::post('/webhooks/twilio/sms/status', [TwilioWebhookController::class, 'smsStatus'])
    ->name('webhooks.twilio.sms.status');

Route::post('/webhooks/twilio/sms/received', [TwilioWebhookController::class, 'smsReceived'])
    ->name('webhooks.twilio.sms.received');

// SendGrid Webhooks (email events)
Route::post('/webhooks/sendgrid', [SendGridWebhookController::class, 'handle'])
    ->name('webhooks.sendgrid');
