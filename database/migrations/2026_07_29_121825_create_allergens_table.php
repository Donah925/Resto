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
        Schema::create('allergens', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'allergène');
            $table->string('nom', 100)->comment('Nom de l\'allergène');
            $table->string('code', 20)->unique()->comment('Code court de l\'allergène (ex: A, B, C)');
            $table->text('description')->nullable()->comment('Description détaillée de l\'allergène');
            $table->string('couleur_icone', 20)->nullable()->comment('Couleur de l\'icône pour affichage');
            $table->string('icone_url', 255)->nullable()->comment('URL de l\'icône de l\'allergène');
            $table->boolean('est_actif')->default(true)->comment('Indique si l\'allergène est actif');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('code');
            $table->index('est_actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergens');
    }
};
