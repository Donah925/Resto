<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use App\Models\ProfilClient;
use App\Models\ProfilLivreur;
use App\Models\ProfilGerant;
use App\Models\Restaurant;
use App\Models\Portefeuille;
use App\Models\Adresse;
use Illuminate\Database\Seeder;

class UtilisateursEtClientsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer des Gérants pour les restaurants existants
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $user = Utilisateur::factory()->create([
                'role' => 'gerant',
                'prenom' => 'Gérant',
                'nom' => $restaurant->getTranslation('nom'),
                'email' => 'gerant.' . $restaurant->slug . '@restaurant-app.com',
            ]);

            ProfilGerant::create([
                'utilisateur_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'salaire' => 150000,
                'taux_commission' => 5.00,
            ]);

            $this->command->info("👨‍💼 Gérant créé pour : {$restaurant->getTranslation('nom')}");
        }

        // 2. Créer des Livreurs
        for ($i = 0; $i < 5; $i++) {
            $user = Utilisateur::factory()->create(['role' => 'livreur']);

            ProfilLivreur::create([
                'utilisateur_id' => $user->id,
                'type_vehicule' => collect(['velo', 'scooter', 'voiture'])->random(),
                'immatriculation' => 'AB-' . $this->faker->numerify('###') . '-CI',
                'note' => $this->faker->randomFloat(2, 4.0, 5.0),
                'est_disponible' => true,
                'latitude_courante' => $this->faker->randomFloat(6, 5.30, 5.35),
                'longitude_courante' => $this->faker->randomFloat(6, -4.05, -3.95),
            ]);
        }
        $this->command->info('🛵 5 Livreurs créés');

        // 3. Créer des Clients avec adresses et portefeuilles
        for ($i = 0; $i < 10; $i++) {
            $profilClient = ProfilClient::factory()->create();
            $user = $profilClient->utilisateur;

            // Créer un portefeuille pour chaque client
            Portefeuille::create([
                'client_id' => $profilClient->id,
                'solde' => $this->faker->randomFloat(2, 5000, 50000),
                'devise' => 'XOF',
            ]);

            // Créer une adresse par défaut
            Adresse::create([
                'client_id' => $profilClient->id,
                'libelle' => 'Maison',
                'adresse_voie' => $this->faker->streetAddress(),
                'ville' => 'Abidjan',
                'region' => 'Abidjan',
                'code_postal' => '00225',
                'pays' => 'Côte d\'Ivoire',
                'latitude' => $this->faker->randomFloat(6, 5.25, 5.40),
                'longitude' => $this->faker->randomFloat(6, -4.10, -3.90),
                'est_par_defaut' => true,
            ]);

            // Mettre à jour le profil avec l'ID de l'adresse par défaut
            $adresse = Adresse::where('client_id', $profilClient->id)->first();
            $profilClient->update(['adresse_par_defaut_id' => $adresse->id]);
        }
        $this->command->info('👥 10 Clients créés avec portefeuille et adresse');
    }
}
