<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::where('client_id', $request->user()->profilClient->id)
            ->with(['messages.auteur.utilisateur'])
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json(['tickets' => $tickets]);
    }

    public function show(Ticket $ticket, Request $request)
    {
        if ($ticket->client_id !== $request->user()->profilClient->id) {
            abort(403);
        }

        $ticket->load(['messages.auteur.utilisateur', 'restaurant']);

        return response()->json(['ticket' => $ticket]);
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'priorite' => 'nullable|in:basse,moyenne,haute',
        ]);

        $ticket = Ticket::create([
            'client_id' => $request->user()->profilClient->id,
            'restaurant_id' => $donnees['restaurant_id'] ?? null,
            'sujet' => $donnees['sujet'],
            'statut' => 'ouvert',
            'priorite' => $donnees['priorite'] ?? 'moyenne',
        ]);

        $ticket->messages()->create([
            'auteur_type' => 'App\\Models\\ProfilClient',
            'auteur_id' => $request->user()->profilClient->id,
            'contenu' => $donnees['message'],
        ]);

        return response()->json([
            'message' => 'Ticket créé avec succès',
            'ticket' => $ticket,
        ], 201);
    }

    public function ajouterMessage(Ticket $ticket, Request $request)
    {
        if ($ticket->client_id !== $request->user()->profilClient->id) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $ticket->messages()->create([
            'auteur_type' => 'App\\Models\\ProfilClient',
            'auteur_id' => $request->user()->profilClient->id,
            'contenu' => $request->message,
        ]);

        if ($ticket->statut === 'ferme') {
            $ticket->update(['statut' => 'ouvert']);
        }

        return response()->json(['message' => 'Message envoyé']);
    }
}
