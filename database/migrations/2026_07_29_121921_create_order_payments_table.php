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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du paiement de commande');
            $table->uuid('order_id')->comment('Référence à la commande');
            $table->string('transaction_id', 100)->nullable()->comment('ID de transaction du processeur de paiement');
            $table->string('methode_paiement', 50)->comment('Méthode de paiement utilisée');
            $table->decimal('montant', 10, 2)->comment('Montant du paiement');
            $table->enum('statut', ['en_attente', 'succes', 'echec', 'annule', 'rembourse'])->default('en_attente')->comment('Statut du paiement');
            $table->text('erreur_message')->nullable()->comment('Message d\'erreur en cas d\'échec');
            $table->json('donnees_reponse')->nullable()->comment('Données de réponse du processeur de paiement au format JSON');
            $table->timestamp('date_paiement')->nullable()->comment('Date et heure du paiement');
            $table->timestamp('date_remboursement')->nullable()->comment('Date et heure du remboursement');
            $table->string('ref_remboursement', 100)->nullable()->comment('Référence du remboursement');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
