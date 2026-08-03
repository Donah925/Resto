<?php

namespace App\Jobs\ThirdParty;

use App\Models\User;
use App\Services\ThirdParty\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendVerificationSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TwilioService $twilioService): void
    {
        // Générer un code OTP à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Log::info('Envoi code de vérification SMS', [
            'user_id' => $this->user->id,
        ]);

        $result = $twilioService->sendVerificationCode($this->user->phone, $code);

        if ($result['success']) {
            // Stocker le code dans le cache pendant 10 minutes
            Cache::put(
                "sms_verification_{$this->user->id}",
                $code,
                now()->addMinutes(10)
            );

            Log::info('Code de vérification envoyé', ['user_id' => $this->user->id]);
        } else {
            Log::warning('Échec envoi code de vérification', [
                'user_id' => $this->user->id,
                'error' => $result['error'] ?? 'Inconnu',
            ]);
        }
    }
}
