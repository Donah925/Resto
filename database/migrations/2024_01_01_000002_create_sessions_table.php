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
        // Table des sessions pour la gestion des connexions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment('Identifiant unique de la session');
            $table->uuid('user_id')->nullable()->index()->comment('Référence à l\'utilisateur connecté');
            $table->string('ip_address', 45)->nullable()->comment('Adresse IP du client');
            $table->text('user_agent')->nullable()->comment('Navigateur et système d\'exploitation');
            $table->longText('payload')->comment('Données de session sérialisées');
            $table->integer('last_activity')->index()->comment('Timestamp de la dernière activité');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
