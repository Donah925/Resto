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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'élément de commande');
            $table->uuid('order_id')->comment('Référence à la commande parent');
            $table->uuid('product_id')->comment('Référence au produit commandé');
            $table->string('nom_produit', 150)->comment('Nom du produit au moment de la commande');
            $table->integer('quantite')->default(1)->comment('Quantité commandée');
            $table->decimal('prix_unitaire', 10, 2)->comment('Prix unitaire au moment de la commande');
            $table->decimal('sous_total', 10, 2)->comment('Sous-total pour cet élément (quantité × prix)');
            $table->text('instructions_speciales')->nullable()->comment('Instructions spéciales pour cet élément');
            $table->json('options_personnalisation')->nullable()->comment('Options de personnalisation sélectionnées au format JSON');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
