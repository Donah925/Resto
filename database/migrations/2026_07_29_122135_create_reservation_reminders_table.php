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
        Schema::create('reservation_reminders', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du rappel de réservation');
            $table->uuid('reservation_id')->comment('Référence à la réservation');
            $table->string('type_rappel', 50)->default('email')->comment('Type de rappel (email, sms)');
            $table->integer('delai_minutes')->comment('Délai avant la réservation en minutes');
            $table->timestamp('date_envoi_prevue')->comment('Date et heure prévues pour l\'envoi du rappel');
            $table->timestamp('date_envoi_effective')->nullable()->comment('Date et heure réelle d\'envoi du rappel');
            $table->enum('statut', ['en_attente', 'envoye', 'echec'])->default('en_attente')->comment('Statut du rappel');
            $table->text('message')->nullable()->comment('Contenu du message de rappel');
            $table->text('erreur_message')->nullable()->comment('Message d\'erreur en cas d\'échec');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->index('reservation_id');
            $table->index('statut');
            $table->index('date_envoi_prevue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_reminders');
    }
};
