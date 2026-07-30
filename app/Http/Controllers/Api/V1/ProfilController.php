<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show(Request $request)
    {
        $utilisateur = $request->user()->load([
            'profilClient.adresseParDefaut',
            'profilClient.portefeuille',
            'profilClient.fidelite',
        ]);

        return response()->json(['utilisateur' => $utilisateur]);
    }

    public function update(Request $request)
    {
        $utilisateur = $request->user();

        $donnees = $request->validate([
            'prenom' => 'nullable|string|max:100',
            'nom' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:utilisateurs,email,' . $utilisateur->id,
            'telephone' => 'nullable|string|max:20',
        ]);

        $utilisateur->update($donnees);

        return response()->json([
            'message' => 'Profil mis à jour',
            'utilisateur' => $utilisateur,
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $chemin = $request->file('avatar')->store('avatars', 'public');

        $request->user()->update(['avatar' => $chemin]);

        return response()->json([
            'message' => 'Avatar uploadé',
            'avatar_url' => asset('storage/' . $chemin),
        ]);
    }
}
