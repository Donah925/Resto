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
        Schema::create('restaurant_delivery_zones', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la zone');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->string('nom')->comment('Nom de la zone');
            $table->json('polygone')->comment('Coordonnées GPS du polygone');
            $table->decimal('frais_livraison', 8, 2)->default(0)->comment('Frais de livraison');
            $table->decimal('frais_livraison_km', 8, 2)->default(0)->comment('Frais par kilomètre');
            $table->integer('delai_min')->default(30)->comment('Délai minimum en minutes');
            $table->integer('delai_max')->default(60)->comment('Délai maximum en minutes');
            $table->decimal('montant_minimum', 8, 2)->default(0)->comment('Montant minimum de commande');
            $table->boolean('active')->default(true)->comment('Zone active');
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
        Schema::dropIfExists('restaurant_delivery_zones');
    }
};
