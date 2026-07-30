<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\ProfilClient;
use App\Models\Portefeuille;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription client
     */
    public function register(Request $request)
    {
        $donnees = $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:utilisateurs,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'mot_de_passe' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'code_parrainage' => ['nullable', 'string', 'exists:profils_client,code_parrainage'],
        ]);

        $utilisateur = Utilisateur::create([
            'prenom' => $donnees['prenom'],
            'nom' => $donnees['nom'],
            'email' => $donnees['email'],
            'telephone' => $donnees['telephone'] ?? null,
            'mot_de_passe' => Hash::make($donnees['mot_de_passe']),
            'role' => 'client',
        ]);

        // Créer le profil client
        $profil = ProfilClient::create([
            'utilisateur_id' => $utilisateur->id,
            'code_parrainage' => $donnees['code_parrainage'] ?? null,
        ]);

        // Créer le portefeuille
        Portefeuille::create([
            'client_id' => $profil->id,
            'solde' => 0,
            'devise' => 'XOF',
        ]);

        // Gérer le parrainage
        if (!empty($donnees['code_parrainage'])) {
            $this->gererParrainage($profil, $donnees['code_parrainage']);
        }

        // Créer le token
        $token = $utilisateur->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'utilisateur' => $utilisateur->load('profilClient'),
            'token' => $token,
        ], 201);
    }

    /**
     * Connexion email/mot de passe
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'mot_de_passe' => ['required'],
            'device_name' => ['nullable', 'string'],
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();

        if (!$utilisateur || !Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (!$utilisateur->estActif()) {
            throw ValidationException::withMessages([
                'email' => ['Votre compte est suspendu.'],
            ]);
        }

        $utilisateur->update(['derniere_connexion_le' => now()]);

        $token = $utilisateur->createToken(
            $request->device_name ?? 'api-token'
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'utilisateur' => $utilisateur->load('profilClient'),
        ]);
    }

    /**
     * Connexion par téléphone (OTP)
     */
    public function loginPhone(Request $request)
    {
        $request->validate([
            'telephone' => ['required', 'string'],
            'otp' => ['nullable', 'string'],
        ]);

        // Si pas d'OTP, on envoie un code
        if (!$request->otp) {
            $otp = rand(100000, 999999);
            cache()->put('otp_phone_' . $request->telephone, $otp, now()->addMinutes(10));

            // TODO: Envoyer SMS via Twilio/OVH
            return response()->json([
                'message' => 'Code OTP envoyé',
                'expires_in' => 600,
            ]);
        }

        // Vérifier l'OTP
        $otpEnCache = cache()->get('otp_phone_' . $request->telephone);

        if ($otpEnCache != $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['Code OTP invalide ou expiré.'],
            ]);
        }

        $utilisateur = Utilisateur::where('telephone', $request->telephone)->first();

        if (!$utilisateur) {
            // Inscription automatique
            return $this->registerPhone($request->telephone);
        }

        cache()->forget('otp_phone_' . $request->telephone);
        $token = $utilisateur->createToken('phone-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'utilisateur' => $utilisateur->load('profilClient'),
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    /**
     * Utilisateur courant
     */
    public function me(Request $request)
    {
        $utilisateur = $request->user()->load([
            'profilClient.adresseParDefaut',
            'profilClient.portefeuille',
        ]);

        return response()->json(['utilisateur' => $utilisateur]);
    }

    /**
     * Mot de passe oublié
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Lien de réinitialisation envoyé']);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Réinitialisation du mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'mot_de_passe' => ['required', 'confirmed', Password::min(8)],
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'mot_de_passe', 'mot_de_passe_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'mot_de_passe' => Hash::make($password),
                ])->save();

                $user->tokens()->delete(); // Révoquer tous les tokens
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Mot de passe réinitialisé']);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Login social (Google, Facebook, Apple)
     */
    public function socialLogin(Request $request, string $provider)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        // TODO: Implémenter avec Laravel Socialite
        // $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->userFromToken($request->access_token);

        return response()->json(['message' => 'Login social - à implémenter'], 501);
    }

    // ===== MÉTHODES PRIVÉES =====

    private function registerPhone(string $telephone)
    {
        $utilisateur = Utilisateur::create([
            'prenom' => 'Client',
            'nom' => 'Mobile',
            'email' => $telephone . '@mobile.local',
            'telephone' => $telephone,
            'telephone_verifie_le' => now(),
            'mot_de_passe' => Hash::make(\Illuminate\Support\Str::random(32)),
            'role' => 'client',
        ]);

        $profil = ProfilClient::create(['utilisateur_id' => $utilisateur->id]);
        Portefeuille::create(['client_id' => $profil->id, 'solde' => 0, 'devise' => 'XOF']);

        $token = $utilisateur->createToken('phone-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'utilisateur' => $utilisateur->load('profilClient'),
            'nouveau_compte' => true,
        ], 201);
    }

    private function gererParrainage(ProfilClient $filleul, string $codeParrainage): void
    {
        $parrain = ProfilClient::where('code_parrainage', $codeParrainage)->first();

        if (!$parrain || $parrain->id === $filleul->id) {
            return;
        }

        \App\Models\Parrainage::create([
            'parrain_id' => $parrain->id,
            'filleul_id' => $filleul->id,
            'code_utilise' => $codeParrainage,
            'bonus_parrain' => 500,
            'bonus_filleul' => 500,
            'statut' => 'en_attente',
        ]);

        $filleul->update(['parraine_par_id' => $parrain->id]);
    }
}
