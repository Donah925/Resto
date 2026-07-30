<?php

namespace Database\Factories;

use App\Enums\RoleUtilisateur;
use App\Enums\StatutUtilisateur;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    // Noms et prénoms réalistes ivoiriens / ouest-africains
    private array $prenoms = ['Kouamé', 'Yao', 'Koffi', 'Adjoua', 'Awa', 'Moussa', 'Fatou', 'Ibrahim', 'Mariam', 'Jean-Marc', 'Clarisse', 'Seydou'];
    private array $noms = ['Kouassi', 'Traoré', 'Diallo', 'Koné', 'N\'Guessan', 'Bamba', 'Touré', 'Yapi', 'Coulibaly', 'Kamara'];

    public function definition(): array
    {
        $prenom = $this->faker->randomElement($this->prenoms);
        $nom = $this->faker->randomElement($this->noms);
        $role = $this->faker->randomElement([RoleUtilisateur::CLIENT, RoleUtilisateur::LIVREUR, RoleUtilisateur::GERANT]);

        // Format téléphone ivoirien réaliste (+225 07 ou 05 XX XX XX XX)
        $indicatif = $this->faker->randomElement(['07', '05', '01']);
        $telephone = '+225 ' . $indicatif . ' ' . $this->faker->numerify('## ## ## ##');

        return [
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => strtolower($prenom . '.' . $nom . '.' . $this->faker->unique()->numberBetween(1, 999) . '@gmail.com'),
            'telephone' => $telephone,
            'telephone_verifie_le' => now(),
            'mot_de_passe' => Hash::make('password123'), // Mot de passe par défaut pour les tests
            'role' => $role,
            'statut' => StatutUtilisateur::ACTIF,
            'langue' => 'fr',
            'fuseau_horaire' => 'Africa/Abidjan',
            'cree_le' => now(),
            'modifie_le' => now(),
        ];
    }

    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleUtilisateur::SUPERADMIN,
            'prenom' => 'Super',
            'nom' => 'Admin',
            'email' => 'superadmin@restaurant-app.com',
        ]);
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleUtilisateur::CLIENT,
        ]);
    }
}
