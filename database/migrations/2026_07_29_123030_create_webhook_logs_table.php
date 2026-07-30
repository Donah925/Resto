<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: webhook_logs - Journaux des appels webhook
     */
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('webhook_id')->constrained('webhooks')->onDelete('cascade');
            $table->string('event')->comment('Événement déclencheur');
            $table->json('payload')->comment('Payload JSON envoyé');
            $table->integer('response_status')->nullable()->comment('Code de statut HTTP de la réponse');
            $table->text('response_body')->nullable()->comment('Corps de la réponse');
            $table->boolean('is_success')->default(false)->comment('Indique si l\'appel a réussi');
            $table->text('error_message')->nullable()->comment('Message d\'erreur en cas d\'échec');
            $table->integer('attempt_number')->default(1)->comment('Numéro de la tentative');
            $table->decimal('response_time_ms', 10, 2)->nullable()->comment('Temps de réponse en millisecondes');
            $table->timestamps();
            
            $table->index(['webhook_id', 'created_at']);
            $table->index(['is_success', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
