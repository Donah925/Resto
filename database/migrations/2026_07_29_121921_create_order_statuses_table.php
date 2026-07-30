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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du statut de commande');
            $table->string('nom', 50)->comment('Nom du statut');
            $table->string('code', 50)->unique()->comment('Code technique du statut');
            $table->text('description')->nullable()->comment('Description du statut');
            $table->string('couleur', 20)->nullable()->comment('Couleur associée au statut pour l\'affichage');
            $table->boolean('est_final')->default(false)->comment('Indique si c\'est un statut final (ex: livrée, annulée)');
            $table->integer('ordre_sequentiel')->default(0)->comment('Ordre séquentiel dans le flux de commande');
            $table->boolean('envoie_notification')->default(true)->comment('Indique si ce statut déclenche une notification');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('code');
            $table->index('ordre_sequentiel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
