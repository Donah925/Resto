<?php

namespace App\Jobs\ThirdParty;

use App\Models\Order;
use App\Services\ThirdParty\SendGridService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [10, 30];

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Order $order
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SendGridService $sendGridService): void
    {
        Log::info('Envoi email confirmation commande', [
            'order_id' => $this->order->id,
        ]);

        $result = $sendGridService->sendOrderConfirmation($this->order);

        if ($result['success']) {
            Log::info('Email de confirmation envoyé', ['order_id' => $this->order->id]);
        } else {
            Log::warning('Échec envoi email de confirmation', [
                'order_id' => $this->order->id,
                'error' => $result['error'] ?? 'Inconnu',
            ]);
        }
    }
}
