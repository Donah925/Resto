<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser les rôles et permissions en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // SuperAdmin
            'voir tableau de bord superadmin',
            'gérer tous les restaurants',
            'gérer tous les utilisateurs',
            'gérer paramètres plateforme',
            'voir analytics globaux',
            
            // Admin
            'voir tableau de bord admin',
            'gérer restaurants assignés',
            'gérer gérants',
            'voir rapports',
            
            // Gérant
            'voir tableau de bord gerant',
            'gérer menu',
            'gérer commandes',
            'gérer réservations',
            'gérer staff restaurant',
            'voir statistiques restaurant',
            
            // Livreur
            'voir commandes livrables',
            'accepter livraison',
            'mettre à jour statut livraison',
            'voir historique livraisons',
            
            // Client
            'passer commande',
            'faire réservation',
            'laisser avis',
            'gérer favoris',
            'utiliser portefeuille',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'voir tableau de bord admin',
            'gérer restaurants assignés',
            'gérer gérants',
            'voir rapports',
        ]);

        $gerant = Role::firstOrCreate(['name' => 'gerant']);
        $gerant->givePermissionTo([
            'voir tableau de bord gerant',
            'gérer menu',
            'gérer commandes',
            'gérer réservations',
            'gérer staff restaurant',
            'voir statistiques restaurant',
        ]);

        $livreur = Role::firstOrCreate(['name' => 'livreur']);
        $livreur->givePermissionTo([
            'voir commandes livrables',
            'accepter livraison',
            'mettre à jour statut livraison',
            'voir historique livraisons',
        ]);

        $client = Role::firstOrCreate(['name' => 'client']);
        $client->givePermissionTo([
            'passer commande',
            'faire réservation',
            'laisser avis',
            'gérer favoris',
            'utiliser portefeuille',
        ]);

        $this->command->info('✅ Rôles et permissions créés avec succès');
    }
}
