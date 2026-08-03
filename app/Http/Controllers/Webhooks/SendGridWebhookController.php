<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\ThirdParty\SendGridService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendGridWebhookController extends Controller
{
    public function __construct(
        protected SendGridService $sendGridService
    ) {}

    /**
     * Webhook pour les événements d'emails (bounce, open, click, etc.)
     */
    public function handle(Request $request)
    {
        $events = $request->all();

        // SendGrid envoie un tableau d'événements
        if (!is_array($events)) {
            $events = [$events];
        }

        foreach ($events as $event) {
            $this->processEvent($event);
        }

        return response()->json(['received' => true]);
    }

    protected function processEvent(array $event)
    {
        $eventType = $event['event'] ?? 'unknown';
        $email = $event['email'] ?? null;
        $timestamp = $event['timestamp'] ?? now()->timestamp;

        Log::info('Événement SendGrid', [
            'type' => $eventType,
            'email' => $email,
            'timestamp' => $timestamp,
        ]);

        match ($eventType) {
            'open' => $this->handleOpen($event),
            'click' => $this->handleClick($event),
            'bounce' => $this->handleBounce($event),
            'dropped' => $this->handleDropped($event),
            'delivered' => $this->handleDelivered($event),
            'spamreport' => $this->handleSpamReport($event),
            'unsubscribe' => $this->handleUnsubscribe($event),
            default => Log::info('Événement SendGrid ignoré', ['type' => $eventType]),
        };
    }

    protected function handleOpen(array $event): void
    {
        // L'email a été ouvert
        Log::info('Email ouvert', ['email' => $event['email']]);
    }

    protected function handleClick(array $event): void
    {
        // Un lien dans l'email a été cliqué
        Log::info('Lien cliqué', [
            'email' => $event['email'],
            'url' => $event['url'] ?? null,
        ]);
    }

    protected function handleBounce(array $event): void
    {
        // Email rebondi (adresse invalide, boîte pleine, etc.)
        Log::warning('Email rebondi', [
            'email' => $event['email'],
            'reason' => $event['reason'] ?? null,
            'type' => $event['type'] ?? 'hard',
        ]);

        // Vous pouvez marquer l'email comme invalide dans votre base de données
    }

    protected function handleDropped(array $event): void
    {
        // Email abandonné par SendGrid
        Log::warning('Email abandonné', [
            'email' => $event['email'],
            'reason' => $event['reason'] ?? null,
        ]);
    }

    protected function handleDelivered(array $event): void
    {
        // Email livré avec succès
        Log::info('Email livré', ['email' => $event['email']]);
    }

    protected function handleSpamReport(array $event): void
    {
        // Utilisateur a marqué l'email comme spam
        Log::warning('Signalement spam', ['email' => $event['email']]);

        // Vous devez désinscrire cet utilisateur immédiatement
    }

    protected function handleUnsubscribe(array $event): void
    {
        // Utilisateur s'est désinscrit
        Log::info('Désinscription', ['email' => $event['email']]);

        // Mettre à jour les préférences de l'utilisateur
    }
}
