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
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du panier');
            $table->uuid('user_id')->nullable()->comment('Référence à l\'utilisateur propriétaire du panier');
            $table->uuid('restaurant_id')->nullable()->comment('Référence au restaurant pour lequel est le panier');
            $table->string('session_id', 100)->nullable()->comment('ID de session pour les paniers invités');
            $table->decimal('sous_total', 10, 2)->default(0)->comment('Sous-total du panier');
            $table->decimal('taxes_estimees', 10, 2)->default(0)->comment('Taxes estimées');
            $table->decimal('frais_livraison_estimes', 10, 2)->default(0)->comment('Frais de livraison estimés');
            $table->decimal('total_estime', 10, 2)->default(0)->comment('Total estimé du panier');
            $table->timestamp('expire_a')->nullable()->comment('Date et heure d\'expiration du panier');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('set null');
            $table->index('user_id');
            $table->index('restaurant_id');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
