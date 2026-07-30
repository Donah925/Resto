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
        Schema::create('restaurant_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique des paramètres');
            $table->uuid('restaurant_id')->unique()->comment('Référence au restaurant');
            $table->boolean('accepte_commandes')->default(true)->comment('Accepte les commandes');
            $table->boolean('accepte_reservations')->default(true)->comment('Accepte les réservations');
            $table->integer('duree_reservation_par_defaut')->default(90)->comment('Durée par défaut en minutes');
            $table->integer('delai_preparation')->default(30)->comment('Délai de préparation en minutes');
            $table->boolean('notification_sms')->default(false)->comment('Notifications SMS activées');
            $table->boolean('notification_email')->default(true)->comment('Notifications email activées');
            $table->string('telephone_notification')->nullable()->comment('Téléphone pour notifications');
            $table->string('email_notification')->nullable()->comment('Email pour notifications');
            $table->json('parametres_personnalises')->nullable()->comment('Paramètres personnalisés JSON');
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->index('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_settings');
    }
};
