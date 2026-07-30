<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Adresse;
use Illuminate\Http\Request;

class AdresseController extends Controller
{
    public function index(Request $request)
    {
        $adresses = $request->user()->profilClient->adresses()->latest()->get();

        return response()->json(['adresses' => $adresses]);
    }

    public function show(Adresse $adresse, Request $request)
    {
        if ($adresse->client_id !== $request->user()->profilClient->id) {
            abort(403);
        }

        return response()->json(['adresse' => $adresse]);
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'libelle' => 'required|string|max:100',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'quartier' => 'nullable|string|max:100',
            'telephone' => 'required|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'est_defaut' => 'nullable|boolean',
        ]);

        $client = $request->user()->profilClient;

        if ($donnees['est_defaut'] ?? false) {
            $client->adresses()->update(['est_defaut' => false]);
        }

        $adresse = $client->adresses()->create($donnees);

        return response()->json([
            'message' => 'Adresse ajoutée',
            'adresse' => $adresse,
        ], 201);
    }

    public function update(Request $request, Adresse $adresse)
    {
        if ($adresse->client_id !== $request->user()->profilClient->id) {
            abort(403);
        }

        $donnees = $request->validate([
            'libelle' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'quartier' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'est_defaut' => 'nullable|boolean',
        ]);

        if (isset($donnees['est_defaut']) && $donnees['est_defaut']) {
            $adresse->client->adresses()->where('id', '!=', $adresse->id)->update(['est_defaut' => false]);
        }

        $adresse->update($donnees);

        return response()->json([
            'message' => 'Adresse mise à jour',
            'adresse' => $adresse,
        ]);
    }

    public function destroy(Adresse $adresse, Request $request)
    {
        if ($adresse->client_id !== $request->user()->profilClient->id) {
            abort(403);
        }

        $adresse->delete();

        return response()->json(['message' => 'Adresse supprimée']);
    }

    public function definirDefaut(Adresse $adresse, Request $request)
    {
        if ($adresse->client_id !== $request->user()->profilClient->id) {
            abort(403);
        }

        $adresse->client->adresses()->update(['est_defaut' => false]);
        $adresse->update(['est_defaut' => true]);

        return response()->json(['message' => 'Adresse définie comme par défaut']);
    }
}
