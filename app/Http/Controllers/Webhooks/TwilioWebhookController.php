<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\ThirdParty\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    public function __construct(
        protected TwilioService $twilioService
    ) {}

    /**
     * Webhook pour les statuts de SMS (Delivery Status)
     */
    public function smsStatus(Request $request)
    {
        $messageSid = $request->input('MessageSid');
        $messageStatus = $request->input('MessageStatus');
        $to = $request->input('To');

        Log::info('Statut SMS Twilio', [
            'message_sid' => $messageSid,
            'status' => $messageStatus,
            'to' => $to,
        ]);

        // Traiter le statut (delivered, failed, sent, etc.)
        // Vous pouvez mettre à jour votre base de données ici

        return response()->json(['received' => true]);
    }

    /**
     * Webhook pour la réception de SMS (si numéro dédié)
     */
    public function smsReceived(Request $request)
    {
        $from = $request->input('From');
        $body = $request->input('Body');
        $messageSid = $request->input('MessageSid');

        Log::info('SMS reçu Twilio', [
            'from' => $from,
            'body' => $body,
            'message_sid' => $messageSid,
        ]);

        // Traiter le SMS reçu (réponses des utilisateurs, STOP, etc.)
        if (strtoupper(trim($body)) === 'STOP') {
            // Gérer la désinscription
            Log::info('Utilisateur a envoyé STOP', ['from' => $from]);
        }

        return response()->json(['received' => true]);
    }
}
