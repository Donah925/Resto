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
        Schema::create('reservation_special_requests', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la demande spéciale de réservation');
            $table->uuid('reservation_id')->comment('Référence à la réservation');
            $table->string('type_demande', 100)->comment('Type de demande spéciale');
            $table->text('description')->comment('Description détaillée de la demande');
            $table->boolean('est_satisfait')->default(false)->comment('Indique si la demande a été satisfaite');
            $table->text('notes_restaurant')->nullable()->comment('Notes internes du restaurant sur cette demande');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->index('reservation_id');
            $table->index('type_demande');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_special_requests');
    }
};
