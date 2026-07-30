<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: marketing_campaigns - Campagnes marketing
     */
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nom de la campagne');
            $table->text('description')->nullable()->comment('Description de la campagne');
            $table->string('type')->default('email')->comment('Type: email, sms, push_notification, banner');
            $table->string('status')->default('draft')->comment('Statut: draft, scheduled, active, paused, completed');
            $table->json('target_audience')->nullable()->comment('Critères JSON de ciblage des utilisateurs');
            $table->foreignUuid('promo_code_id')->nullable()->constrained('promo_codes')->onDelete('set null');
            $table->text('content')->nullable()->comment('Contenu de la campagne (HTML ou texte)');
            $table->string('subject')->nullable()->comment('Objet pour les emails');
            $table->datetime('scheduled_at')->nullable()->comment('Date de programmation');
            $table->datetime('started_at')->nullable()->comment('Date de début effective');
            $table->datetime('ended_at')->nullable()->comment('Date de fin');
            $table->integer('sent_count')->default(0)->comment('Nombre d\'envois effectués');
            $table->integer('opened_count')->default(0)->comment('Nombre d\'ouvertures');
            $table->integer('clicked_count')->default(0)->comment('Nombre de clics');
            $table->integer('converted_count')->default(0)->comment('Nombre de conversions');
            $table->decimal('budget', 10, 2)->nullable()->comment('Budget alloué à la campagne');
            $table->foreignUuid('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['status', 'type']);
            $table->index(['scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
