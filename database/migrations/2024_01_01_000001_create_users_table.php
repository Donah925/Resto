<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table des utilisateurs principaux
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'utilisateur');
            $table->string('nom')->comment('Nom de l\'utilisateur');
            $table->string('prenom')->comment('Prénom de l\'utilisateur');
            $table->string('email')->unique()->comment('Adresse email unique');
            $table->timestamp('email_verified_at')->nullable()->comment('Date de vérification de l\'email');
            $table->string('password')->comment('Mot de passe haché');
            $table->string('telephone')->nullable()->comment('Numéro de téléphone');
            $table->text('adresse')->nullable()->comment('Adresse postale complète');
            $table->string('ville')->nullable()->comment('Ville de résidence');
            $table->string('code_postal')->nullable()->comment('Code postal');
            $table->string('pays')->default('France')->comment('Pays de résidence');
            $table->date('date_naissance')->nullable()->comment('Date de naissance');
            $table->enum('genre', ['homme', 'femme', 'autre'])->nullable()->comment('Genre de l\'utilisateur');
            $table->string('photo_profil')->nullable()->comment('URL de la photo de profil');
            $table->json('preferences')->nullable()->comment('Préférences utilisateur (allergies, régimes, etc.)');
            $table->boolean('actif')->default(true)->comment('Statut d\'activation du compte');
            $table->timestamp('derniere_connexion')->nullable()->comment('Date de dernière connexion');
            $table->rememberToken()->comment('Token pour la fonction "se souvenir de moi"');
            $table->timestamps();
            $table->softDeletes()->comment('Suppression douce pour récupération des comptes');
            
            $table->index('email');
            $table->index('telephone');
            $table->index('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
