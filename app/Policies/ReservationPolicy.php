<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Reservation;

class ReservationPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des réservations.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT']);
    }

    /**
     * Déterminer si l'utilisateur peut voir une réservation spécifique.
     */
    public function view(Utilisateur $user, Reservation $reservation): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le gérant peut voir les réservations de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $reservation->restaurant_id === $user->gerantDe->id;
        }

        // Le client peut voir ses propres réservations
        if ($user->hasRole('CLIENT')) {
            return $reservation->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut créer une réservation.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole('CLIENT');
    }

    /**
     * Déterminer si l'utilisateur peut modifier une réservation.
     */
    public function update(Utilisateur $user, Reservation $reservation): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Le gérant peut modifier les réservations de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $reservation->restaurant_id === $user->gerantDe->id;
        }

        // Le client peut modifier sa réservation avant la date
        if ($user->hasRole('CLIENT')) {
            if ($reservation->client_id !== $user->profilClient->user_id) {
                return false;
            }
            return $reservation->date_reservation >= now();
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut annuler une réservation.
     */
    public function cancel(Utilisateur $user, Reservation $reservation): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le gérant peut annuler les réservations de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $reservation->restaurant_id === $user->gerantDe->id;
        }

        // Le client peut annuler sa réservation (avec politique de délai)
        if ($user->hasRole('CLIENT')) {
            if ($reservation->client_id !== $user->profilClient->user_id) {
                return false;
            }
            // Annulation possible jusqu'à 2h avant
            return $reservation->date_reservation >= now()->addHours(2);
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut confirmer une réservation.
     */
    public function confirm(Utilisateur $user, Reservation $reservation): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Seul le gérant du restaurant peut confirmer
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $reservation->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }
}
