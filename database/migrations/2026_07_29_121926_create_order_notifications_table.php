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
        Schema::create('order_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la notification de commande');
            $table->uuid('order_id')->comment('Référence à la commande');
            $table->string('type_notification', 50)->comment('Type de notification (email, sms, push)');
            $table->string('destinataire', 150)->comment('Adresse email ou numéro de téléphone du destinataire');
            $table->string('sujet', 200)->nullable()->comment('Sujet de la notification');
            $table->text('message')->comment('Contenu du message de notification');
            $table->enum('statut', ['en_attente', 'envoyee', 'livree', 'echec'])->default('en_attente')->comment('Statut d\'envoi de la notification');
            $table->timestamp('date_envoi')->nullable()->comment('Date et heure d\'envoi de la notification');
            $table->text('erreur_message')->nullable()->comment('Message d\'erreur en cas d\'échec d\'envoi');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('order_id');
            $table->index('type_notification');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_notifications');
    }
};
