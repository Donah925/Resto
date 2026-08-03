<?php

namespace App\Services\Sms;

interface SmsSenderInterface
{
    /**
     * Envoyer un SMS
     *
     * @param string $to Le numéro de téléphone
     * @param string $message Le message à envoyer
     * @return array Résultat de l'envoi
     */
    public function send(string $to, string $message): array;

    /**
     * Envoyer un code de vérification OTP
     *
     * @param string $to Le numéro de téléphone
     * @param string $code Le code à envoyer
     * @return array Résultat de l'envoi
     */
    public function sendVerificationCode(string $to, string $code): array;

    /**
     * Obtenir le statut d'un message
     *
     * @param string $messageId L'identifiant du message
     * @return array Statut du message
     */
    public function getMessageStatus(string $messageId): array;

    /**
     * Nom du service SMS
     *
     * @return string
     */
    public function getName(): string;
}
