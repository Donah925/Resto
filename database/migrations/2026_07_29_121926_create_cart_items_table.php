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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'élément du panier');
            $table->uuid('cart_id')->comment('Référence au panier parent');
            $table->uuid('product_id')->comment('Référence au produit ajouté au panier');
            $table->integer('quantite')->default(1)->comment('Quantité du produit dans le panier');
            $table->decimal('prix_unitaire', 10, 2)->comment('Prix unitaire au moment de l\'ajout');
            $table->decimal('sous_total', 10, 2)->comment('Sous-total pour cet élément (quantité × prix)');
            $table->text('instructions_speciales')->nullable()->comment('Instructions spéciales pour cet élément');
            $table->json('options_personnalisation')->nullable()->comment('Options de personnalisation sélectionnées au format JSON');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('cart_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
