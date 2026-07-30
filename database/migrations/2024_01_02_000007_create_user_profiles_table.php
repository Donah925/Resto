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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du profil');
            $table->uuid('user_id')->comment('Référence à l\'utilisateur');
            $table->string('type_client')->default('particulier')->comment('Type de client (particulier, professionnel)');
            $table->json('allergies')->nullable()->comment('Liste des allergies alimentaires');
            $table->json('regimes')->nullable()->comment('Régimes alimentaires préférés');
            $table->integer('points_fidelite')->default(0)->comment('Points de fidélité accumulés');
            $table->enum('statut_fidelite', ['bronze', 'argent', 'or', 'platine'])->default('bronze')->comment('Niveau de fidélité');
            $table->date('date_adhesion_fidelite')->nullable()->comment('Date d\'adhésion au programme fidélité');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('statut_fidelite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
