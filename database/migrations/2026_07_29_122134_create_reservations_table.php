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
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la réservation');
            $table->string('reference', 50)->unique()->comment('Numéro de référence unique de la réservation');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->uuid('user_id')->nullable()->comment('Référence à l\'utilisateur client');
            $table->string('nom_client', 100)->comment('Nom du client pour la réservation');
            $table->string('email_client', 150)->comment('Email du client');
            $table->string('telephone_client', 20)->comment('Numéro de téléphone du client');
            $table->integer('nombre_personnes')->default(2)->comment('Nombre de personnes pour la réservation');
            $table->date('date_reservation')->comment('Date de la réservation');
            $table->time('heure_reservation')->comment('Heure de la réservation');
            $table->enum('statut', ['en_attente', 'confirmee', 'terminee', 'annulee', 'no_show'])->default('en_attente')->comment('Statut de la réservation');
            $table->text('instructions_speciales')->nullable()->comment('Instructions spéciales pour la réservation');
            $table->boolean('est_occasion_speciale')->default(false)->comment('Indique si c\'est une occasion spéciale');
            $table->string('type_occasion', 100)->nullable()->comment('Type d\'occasion spéciale (anniversaire, rendez-vous, etc.)');
            $table->uuid('table_id')->nullable()->comment('Référence à la table assignée');
            $table->timestamp('date_confirmation')->nullable()->comment('Date et heure de confirmation');
            $table->timestamp('date_annulation')->nullable()->comment('Date et heure d\'annulation');
            $table->string('motif_annulation', 200)->nullable()->comment('Motif de l\'annulation');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('restaurant_id');
            $table->index('user_id');
            $table->index('date_reservation');
            $table->index('statut');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
