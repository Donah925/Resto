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
        Schema::create('user_payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du moyen de paiement');
            $table->uuid('user_id')->comment('Référence à l\'utilisateur');
            $table->string('type')->comment('Type de paiement (carte, paypal, etc.)');
            $table->string('derniers_chiffres', 4)->nullable()->comment('Derniers chiffres de la carte');
            $table->string('marque')->nullable()->comment('Marque de la carte (Visa, Mastercard)');
            $table->string('mois_expiration', 2)->nullable()->comment('Mois d\'expiration');
            $table->string('annee_expiration', 4)->nullable()->comment('Année d\'expiration');
            $table->string('nom_titulaire')->nullable()->comment('Nom du titulaire');
            $table->boolean('par_defaut')->default(false)->comment('Moyen de paiement par défaut');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_payment_methods');
    }
};
