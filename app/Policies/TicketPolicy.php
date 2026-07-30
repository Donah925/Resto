<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Ticket;

class TicketPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des tickets.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut voir un ticket spécifique.
     */
    public function view(Utilisateur $user, Ticket $ticket): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le client ne peut voir que ses propres tickets
        if ($user->hasRole('CLIENT')) {
            return $ticket->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut créer un ticket.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole('CLIENT');
    }

    /**
     * Déterminer si l'utilisateur peut répondre à un ticket.
     */
    public function reply(Utilisateur $user, Ticket $ticket): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le client peut répondre à ses propres tickets
        if ($user->hasRole('CLIENT')) {
            return $ticket->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut changer le statut d'un ticket.
     */
    public function changeStatus(Utilisateur $user, Ticket $ticket): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut assigner un ticket.
     */
    public function assign(Utilisateur $user, Ticket $ticket): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut fermer un ticket.
     */
    public function close(Utilisateur $user, Ticket $ticket): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le client peut fermer son propre ticket
        if ($user->hasRole('CLIENT')) {
            return $ticket->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un ticket.
     */
    public function delete(Utilisateur $user, Ticket $ticket): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }
}
