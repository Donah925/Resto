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
        Schema::create('order_addons', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'option supplémentaire de commande');
            $table->uuid('order_item_id')->comment('Référence à l\'élément de commande');
            $table->uuid('addon_id')->comment('Référence à l\'option supplémentaire du produit');
            $table->string('nom_option', 100)->comment('Nom de l\'option au moment de la commande');
            $table->decimal('prix', 10, 2)->default(0)->comment('Prix de l\'option au moment de la commande');
            $table->integer('quantite')->default(1)->comment('Quantité de cette option');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('addon_id')->references('id')->on('product_addons')->onDelete('restrict');
            $table->index('order_item_id');
            $table->index('addon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addons');
    }
};
