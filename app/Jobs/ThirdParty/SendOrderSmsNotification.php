<?php

namespace App\Jobs\ThirdParty;

use App\Models\Order;
use App\Services\ThirdParty\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderSmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Order $order
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TwilioService $twilioService): void
    {
        Log::info('Envoi notification SMS commande', [
            'order_id' => $this->order->id,
        ]);

        $result = $twilioService->sendOrderNotification(
            $this->order,
            $this->order->user
        );

        if ($result['success']) {
            Log::info('Notification SMS envoyée', ['order_id' => $this->order->id]);
        } else {
            Log::warning('Échec envoi notification SMS', [
                'order_id' => $this->order->id,
                'error' => $result['error'] ?? 'Inconnu',
            ]);
        }
    }
}
