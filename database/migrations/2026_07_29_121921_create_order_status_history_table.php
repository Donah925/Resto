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
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'historique de statut');
            $table->uuid('order_id')->comment('Référence à la commande');
            $table->uuid('status_id')->comment('Référence au statut appliqué');
            $table->uuid('user_id')->nullable()->comment('Référence à l\'utilisateur ayant effectué le changement');
            $table->text('commentaire')->nullable()->comment('Commentaire sur le changement de statut');
            $table->timestamp('date_changement')->useCurrent()->comment('Date et heure du changement de statut');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('order_statuses')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('order_id');
            $table->index('status_id');
            $table->index('date_changement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
};
