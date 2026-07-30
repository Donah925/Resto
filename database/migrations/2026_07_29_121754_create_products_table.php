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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du produit');
            $table->uuid('restaurant_id')->comment('Référence au restaurant propriétaire du produit');
            $table->uuid('category_id')->nullable()->comment('Référence à la catégorie du produit');
            $table->string('nom', 150)->comment('Nom du produit');
            $table->text('description')->nullable()->comment('Description détaillée du produit');
            $table->decimal('prix', 10, 2)->comment('Prix unitaire du produit');
            $table->decimal('prix_promotionnel', 10, 2)->nullable()->comment('Prix promotionnel réduit');
            $table->date('date_debut_promo')->nullable()->comment('Date de début de la promotion');
            $table->date('date_fin_promo')->nullable()->comment('Date de fin de la promotion');
            $table->string('unite_mesure', 50)->default('pièce')->comment('Unité de mesure (pièce, kg, litre, etc.)');
            $table->integer('stock_disponible')->default(0)->comment('Quantité en stock disponible');
            $table->integer('stock_minimum')->default(5)->comment('Niveau minimum de stock avant alerte');
            $table->boolean('est_disponible')->default(true)->comment('Indique si le produit est disponible à la commande');
            $table->boolean('est_vegetarien')->default(false)->comment('Indique si le produit est végétarien');
            $table->boolean('est_vegan')->default(false)->comment('Indique si le produit est vegan');
            $table->boolean('est_sans_gluten')->default(false)->comment('Indique si le produit est sans gluten');
            $table->json('allergenes')->nullable()->comment('Liste des allergènes au format JSON');
            $table->json('valeurs_nutritionnelles')->nullable()->comment('Informations nutritionnelles au format JSON');
            $table->json('images')->nullable()->comment('Tableau d\'URLs des images du produit');
            $table->integer('temps_preparation_minutes')->default(15)->comment('Temps de préparation estimé en minutes');
            $table->integer('ordre_affichage')->default(0)->comment('Ordre d\'affichage dans le menu');
            $table->json('options_personnalisation')->nullable()->comment('Options de personnalisation disponibles au format JSON');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->index('restaurant_id');
            $table->index('category_id');
            $table->index('est_disponible');
            $table->index('nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
