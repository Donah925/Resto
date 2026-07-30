<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: notifications - Notifications envoyées aux utilisateurs
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->comment('Type: email, sms, push_notification, in_app');
            $table->string('title')->comment('Titre de la notification');
            $table->text('message')->comment('Contenu du message');
            $table->json('data')->nullable()->comment('Données JSON additionnelles');
            $table->string('action_url')->nullable()->comment('URL d\'action lors du clic');
            $table->boolean('is_read')->default(false)->comment('Indique si la notification a été lue');
            $table->datetime('read_at')->nullable()->comment('Date de lecture');
            $table->foreignUuid('related_model_id')->nullable()->comment('ID du modèle lié (commande, réservation, etc.)');
            $table->string('related_model_type')->nullable()->comment('Type du modèle lié (App\\Models\\Order, etc.)');
            $table->timestamps();
            
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['type', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
