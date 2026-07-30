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
        Schema::create('reservation_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du statut de réservation');
            $table->string('nom', 50)->comment('Nom du statut');
            $table->string('code', 50)->unique()->comment('Code technique du statut');
            $table->text('description')->nullable()->comment('Description du statut');
            $table->string('couleur', 20)->nullable()->comment('Couleur associée au statut pour l\'affichage');
            $table->boolean('est_final')->default(false)->comment('Indique si c\'est un statut final');
            $table->boolean('envoie_notification')->default(true)->comment('Indique si ce statut déclenche une notification');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_statuses');
    }
};
