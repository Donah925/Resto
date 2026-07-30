<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = $request->user()->profilClient
            ->reservations()
            ->with(['restaurant', 'table'])
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json(['reservations' => $reservations]);
    }

    public function show(Reservation $reservation, Request $request)
    {
        $this->autoriserAcces($reservation, $request->user());

        $reservation->load(['restaurant', 'table', 'client.utilisateur']);

        return response()->json(['reservation' => $reservation]);
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'date' => 'required|date|after_or_equal:today',
            'heure' => 'required|date_format:H:i',
            'nombre_personnes' => 'required|integer|min:1|max:50',
            'table_id' => 'nullable|exists:tables_restaurant,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $client = $request->user()->profilClient;

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'restaurant_id' => $donnees['restaurant_id'],
            'table_id' => $donnees['table_id'] ?? null,
            'date' => $donnees['date'],
            'heure' => $donnees['heure'],
            'nombre_personnes' => $donnees['nombre_personnes'],
            'notes' => $donnees['notes'] ?? null,
            'statut' => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Réservation créée avec succès',
            'reservation' => $reservation->load(['restaurant']),
        ], 201);
    }

    public function annuler(Reservation $reservation, Request $request)
    {
        $this->autoriserAcces($reservation, $request->user());

        if (!$reservation->estAnnulable()) {
            return response()->json([
                'message' => 'Cette réservation ne peut plus être annulée',
            ], 422);
        }

        $reservation->update(['statut' => 'annulee']);

        return response()->json(['message' => 'Réservation annulée']);
    }

    public function creneauxDisponibles(string $restaurant, Request $request)
    {
        // Logique pour récupérer les créneaux disponibles
        return response()->json(['creneaux' => []]);
    }

    private function autoriserAcces(Reservation $reservation, $user): void
    {
        if ($reservation->client_id !== $user->profilClient->id) {
            abort(403, 'Vous n\'avez pas accès à cette réservation');
        }
    }
}
