<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du restaurant');
            $table->uuid('proprietaire_id')->comment('Référence au propriétaire');
            $table->string('nom')->comment('Nom du restaurant');
            $table->string('slug')->unique()->comment('URL friendly name');
            $table->text('description')->nullable()->comment('Description détaillée');
            $table->text('histoire')->nullable()->comment('Histoire du restaurant');
            $table->string('email')->nullable()->comment('Email de contact');
            $table->string('telephone')->nullable()->comment('Numéro de téléphone');
            $table->text('adresse')->comment('Adresse complète');
            $table->string('ville')->comment('Ville');
            $table->string('code_postal')->comment('Code postal');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude GPS');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude GPS');
            $table->json('types_cuisine')->comment('Types de cuisine proposés');
            $table->integer('fourchette_prix')->default(2)->comment('Fourchette de prix (1-4)');
            $table->time('heure_ouverture')->nullable()->comment('Heure d\'ouverture');
            $table->time('heure_fermeture')->nullable()->comment('Heure de fermeture');
            $table->json('jours_fermeture')->nullable()->comment('Jours de fermeture hebdomadaire');
            $table->integer('capacité_max')->nullable()->comment('Capacité maximale en couverts');
            $table->boolean('livraison_disponible')->default(false)->comment('Livraison disponible');
            $table->boolean('click_and_collect')->default(false)->comment('Click & Collect disponible');
            $table->boolean('reservation_requise')->default(false)->comment('Réservation requise');
            $table->json('equipements')->nullable()->comment('Équipements (wifi, parking, terrasse)');
            $table->json('options_alimentaires')->nullable()->comment('Options (végétarien, vegan, halal)');
            $table->string('logo')->nullable()->comment('URL du logo');
            $table->json('images')->nullable()->comment('Galerie d\'images');
            $table->string('site_web')->nullable()->comment('Site web officiel');
            $table->json('reseaux_sociaux')->nullable()->comment('Liens réseaux sociaux');
            $table->enum('statut_verification', ['en_attente', 'verifie', 'rejete'])->default('en_attente')->comment('Statut de vérification');
            $table->timestamp('date_verification')->nullable()->comment('Date de vérification');
            $table->decimal('note_moyenne', 3, 2)->default(0)->comment('Note moyenne des avis');
            $table->integer('nombre_avis')->default(0)->comment('Nombre total d\'avis');
            $table->boolean('actif')->default(true)->comment('Restaurant actif');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('proprietaire_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('slug');
            $table->index('ville');
            $table->index('statut_verification');
            $table->index('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
