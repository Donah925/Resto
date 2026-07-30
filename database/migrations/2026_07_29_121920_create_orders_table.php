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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la commande');
            $table->string('reference', 50)->unique()->comment('Numéro de référence unique de la commande');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->uuid('user_id')->nullable()->comment('Référence à l\'utilisateur client (null pour commande invité)');
            $table->uuid('address_id')->nullable()->comment('Référence à l\'adresse de livraison');
            $table->enum('type_commande', ['sur_place', 'emporter', 'livraison'])->default('sur_place')->comment('Type de commande');
            $table->enum('statut', ['en_attente', 'confirmee', 'en_preparation', 'prete', 'en_livraison', 'livree', 'annulee', 'remboursee'])->default('en_attente')->comment('Statut actuel de la commande');
            $table->decimal('sous_total', 10, 2)->comment('Sous-total avant taxes et frais');
            $table->decimal('taxes', 10, 2)->default(0)->comment('Montant des taxes');
            $table->decimal('frais_livraison', 10, 2)->default(0)->comment('Frais de livraison');
            $table->decimal('frais_service', 10, 2)->default(0)->comment('Frais de service');
            $table->decimal('reduction', 10, 2)->default(0)->comment('Montant de la réduction appliquée');
            $table->decimal('total', 10, 2)->comment('Montant total de la commande');
            $table->string('mode_paiement', 50)->nullable()->comment('Mode de paiement choisi');
            $table->enum('statut_paiement', ['en_attente', 'paye', 'echec', 'rembourse'])->default('en_attente')->comment('Statut du paiement');
            $table->text('instructions_speciales')->nullable()->comment('Instructions spéciales pour la commande');
            $table->integer('nombre_personnes')->default(1)->comment('Nombre de personnes pour la commande');
            $table->timestamp('date_prevue')->nullable()->comment('Date et heure prévues pour la commande');
            $table->timestamp('date_confirmation')->nullable()->comment('Date et heure de confirmation de la commande');
            $table->timestamp('date_preparation')->nullable()->comment('Date et heure de début de préparation');
            $table->timestamp('date_pret')->nullable()->comment('Date et heure où la commande est prête');
            $table->timestamp('date_livraison')->nullable()->comment('Date et heure de livraison');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('address_id')->references('id')->on('user_addresses')->onDelete('set null');
            $table->index('restaurant_id');
            $table->index('user_id');
            $table->index('statut');
            $table->index('type_commande');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
