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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'adresse');
            $table->uuid('user_id')->comment('Référence à l\'utilisateur');
            $table->string('nom')->comment('Nom de l\'adresse (domicile, travail, etc.)');
            $table->text('rue')->comment('Numéro et nom de rue');
            $table->string('complement')->nullable()->comment('Complément d\'adresse');
            $table->string('ville')->comment('Ville');
            $table->string('code_postal')->comment('Code postal');
            $table->string('pays')->default('France')->comment('Pays');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude GPS');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude GPS');
            $table->string('instructions')->nullable()->comment('Instructions de livraison');
            $table->boolean('par_defaut')->default(false)->comment('Adresse par défaut');
            $table->boolean('livraison_activee')->default(true)->comment('Livraison activée pour cette adresse');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('par_defaut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
