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
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du menu');
            $table->uuid('restaurant_id')->comment('Référence au restaurant propriétaire du menu');
            $table->string('nom', 100)->comment('Nom du menu');
            $table->text('description')->nullable()->comment('Description du menu');
            $table->string('type', 50)->default('regulier')->comment('Type de menu (déjeuner, dîner, brunch, etc.)');
            $table->decimal('prix_fixe', 10, 2)->nullable()->comment('Prix fixe pour le menu complet');
            $table->time('heure_debut')->nullable()->comment('Heure de début de disponibilité du menu');
            $table->time('heure_fin')->nullable()->comment('Heure de fin de disponibilité du menu');
            $table->date('date_debut')->nullable()->comment('Date de début de validité du menu');
            $table->date('date_fin')->nullable()->comment('Date de fin de validité du menu');
            $table->boolean('est_actif')->default(true)->comment('Indique si le menu est actif');
            $table->boolean('est_vegetarien')->default(false)->comment('Indique si le menu est végétarien');
            $table->integer('nombre_couverts')->default(1)->comment('Nombre de couverts inclus dans le menu');
            $table->json('images')->nullable()->comment('Tableau d\'URLs des images du menu');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->index('restaurant_id');
            $table->index('type');
            $table->index('est_actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
