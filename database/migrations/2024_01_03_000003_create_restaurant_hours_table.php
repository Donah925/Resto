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
        Schema::create('restaurant_hours', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique des horaires');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->enum('jour', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'])->comment('Jour de la semaine');
            $table->time('ouverture_midi_debut')->nullable()->comment('Début service midi');
            $table->time('ouverture_midi_fin')->nullable()->comment('Fin service midi');
            $table->time('ouverture_soir_debut')->nullable()->comment('Début service soir');
            $table->time('ouverture_soir_fin')->nullable()->comment('Fin service soir');
            $table->boolean('ferme')->default(false)->comment('Fermé ce jour');
            $table->text('notes')->nullable()->comment('Notes spéciales');
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->unique(['restaurant_id', 'jour'], 'unique_restaurant_day');
            $table->index('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_hours');
    }
};
