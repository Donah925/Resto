<?php

namespace App\Jobs\ThirdParty;

use App\Models\Order;
use App\Services\ThirdParty\StripeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStripePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Order $order,
        protected string $sessionId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(StripeService $stripeService): void
    {
        Log::info('Traitement paiement Stripe', [
            'order_id' => $this->order->id,
            'session_id' => $this->sessionId,
        ]);

        $result = $stripeService->getSessionStatus($this->sessionId);

        if ($result['success'] && $result['paid']) {
            $this->order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'stripe_session_id' => $this->sessionId,
            ]);

            event(new \App\Events\OrderConfirmed($this->order));

            Log::info('Paiement Stripe confirmé', ['order_id' => $this->order->id]);
        } else {
            Log::warning('Paiement Stripe non confirmé', [
                'order_id' => $this->order->id,
                'result' => $result,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Échec traitement paiement Stripe', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);

        // Notification aux administrateurs en cas d'échec
    }
}
