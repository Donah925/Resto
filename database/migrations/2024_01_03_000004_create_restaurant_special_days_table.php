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
        Schema::create('restaurant_special_days', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du jour spécial');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->date('date')->comment('Date spécifique');
            $table->enum('type', ['ferme_exceptionnel', 'horaire_special', 'evenement'])->comment('Type de jour spécial');
            $table->time('ouverture_debut')->nullable()->comment('Heure d\'ouverture');
            $table->time('ouverture_fin')->nullable()->comment('Heure de fermeture');
            $table->text('motif')->nullable()->comment('Motif de la modification');
            $table->string('evenement_nom')->nullable()->comment('Nom de l\'événement');
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->index('restaurant_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_special_days');
    }
};
