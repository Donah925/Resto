<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use App\Models\ProfilSuperAdmin;
use App\Models\Portefeuille;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = Utilisateur::create([
            'prenom' => 'Administrateur',
            'nom' => 'Système',
            'email' => 'admin@restaurant-app.com',
            'telephone' => '+225 07 00 00 00 00',
            'telephone_verifie_le' => now(),
            'mot_de_passe' => Hash::make('Admin@12345'),
            'role' => 'superadmin',
            'statut' => 'actif',
            'langue' => 'fr',
            'fuseau_horaire' => 'Africa/Abidjan',
        ]);

        ProfilSuperAdmin::create([
            'utilisateur_id' => $user->id,
            'permissions' => ['*'],
            'notes' => 'Compte principal du super administrateur',
        ]);

        $this->command->info('✅ Super Admin créé : admin@restaurant-app.com / Admin@12345');
    }
}
