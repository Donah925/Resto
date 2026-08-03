<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\StripeGateway;
use App\Services\Payment\Gateways\PayPalGateway;
use App\Services\Payment\Gateways\MtnMomoGateway;
use App\Services\Payment\Gateways\MoovMoneyGateway;
use App\Services\Payment\Gateways\OrangeMoneyGateway;
use App\Services\Payment\Gateways\WaveGateway;
use App\Services\Payment\Gateways\PayeerGateway;
use App\Services\Payment\Gateways\PayoneerGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    protected array $gateways = [];

    public function __construct()
    {
        $this->gateways = config('payment.gateways', []);
    }

    /**
     * Créer une instance de gateway de paiement
     *
     * @param string $gateway Le nom du gateway
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public function create(string $gateway): PaymentGatewayInterface
    {
        return match (strtolower($gateway)) {
            'stripe' => new StripeGateway(),
            'paypal' => new PayPalGateway(),
            'mtn_momo' => new MtnMomoGateway(),
            'moov_money' => new MoovMoneyGateway(),
            'orange_money' => new OrangeMoneyGateway(),
            'wave' => new WaveGateway(),
            'payeer' => new PayeerGateway(),
            'payoneer' => new PayoneerGateway(),
            default => throw new InvalidArgumentException("Le gateway de paiement '{$gateway}' n'est pas supporté."),
        };
    }

    /**
     * Obtenir la liste des gateways disponibles
     *
     * @return array
     */
    public function getAvailableGateways(): array
    {
        return [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'mtn_momo' => 'MTN Mobile Money',
            'moov_money' => 'Moov Money',
            'orange_money' => 'Orange Money',
            'wave' => 'Wave',
            'payeer' => 'Payeer',
            'payoneer' => 'Payoneer',
        ];
    }

    /**
     * Vérifier si un gateway est disponible
     *
     * @param string $gateway
     * @return bool
     */
    public function isAvailable(string $gateway): bool
    {
        return array_key_exists(strtolower($gateway), $this->getAvailableGateways());
    }

    /**
     * Obtenir le gateway par défaut
     *
     * @return PaymentGatewayInterface
     */
    public function getDefault(): PaymentGatewayInterface
    {
        $default = config('payment.default', 'stripe');
        
        return $this->create($default);
    }
}
