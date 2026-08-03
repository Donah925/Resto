<?php

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Initialiser un paiement
     *
     * @param array $data Les données de paiement
     * @return array Résultat de l'initialisation
     */
    public function initialize(array $data): array;

    /**
     * Capturer un paiement
     *
     * @param string $paymentId L'identifiant du paiement
     * @return array Résultat de la capture
     */
    public function capture(string $paymentId): array;

    /**
     * Vérifier le statut d'un paiement
     *
     * @param string $paymentId L'identifiant du paiement
     * @return array Statut du paiement
     */
    public function getStatus(string $paymentId): array;

    /**
     * Rembourser un paiement
     *
     * @param string $paymentId L'identifiant du paiement
     * @param float|null $amount Montant à rembourser (null pour remboursement total)
     * @return array Résultat du remboursement
     */
    public function refund(string $paymentId, ?float $amount = null): array;

    /**
     * Traiter un webhook
     *
     * @param array $payload Les données du webhook
     * @return array Résultat du traitement
     */
    public function handleWebhook(array $payload): array;

    /**
     * Nom du gateway
     *
     * @return string
     */
    public function getName(): string;
}
