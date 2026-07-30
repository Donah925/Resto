<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Début du remplissage de la base de données...');

        // 1. Données de base (Allergènes, Rôles, etc.)
        $this->call([
            RoleAndPermissionSeeder::class, // (À créer si tu utilises Spatie, sinon ignore)
            AllergeneSeeder::class,
        ]);

        // 2. Entités principales
        $this->call([
            SuperAdminSeeder::class,
            RestaurantMenuSeeder::class,
            UtilisateursEtClientsSeeder::class,
        ]);

        // 3. Données de test (Commandes, Réservations) - Optionnel mais recommandé
        // $this->call(CommandeSeeder::class);

        $this->command->info('✅ Remplissage de la base de données terminé avec succès !');
        $this->command->info('📧 SuperAdmin: admin@restaurant-app.com | Mot de passe: Admin@12345');
    }
}
