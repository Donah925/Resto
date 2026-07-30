<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: delivery_addresses - Adresses de livraison des utilisateurs
     */
    public function up(): void
    {
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label')->comment('Libellé de l\'adresse (ex: Domicile, Travail)');
            $table->string('street')->comment('Rue et numéro');
            $table->string('complement')->nullable()->comment('Complément d\'adresse');
            $table->string('city')->comment('Ville');
            $table->string('postal_code')->comment('Code postal');
            $table->string('country')->default('France')->comment('Pays');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude pour la géolocalisation');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude pour la géolocalisation');
            $table->string('phone')->comment('Numéro de téléphone pour la livraison');
            $table->string('instructions')->nullable()->comment('Instructions de livraison (ex: sonner, laisser devant la porte)');
            $table->boolean('is_default')->default(false)->comment('Indique si c\'est l\'adresse par défaut');
            $table->timestamps();
            
            $table->index(['user_id', 'is_default']);
            $table->index(['city', 'postal_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_addresses');
    }
};
