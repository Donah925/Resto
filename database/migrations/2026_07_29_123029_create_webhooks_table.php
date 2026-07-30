<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: webhooks - Webhooks pour les intégrations externes
     */
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nom descriptif du webhook');
            $table->string('url')->comment('URL de destination du webhook');
            $table->string('event')->comment('Événement déclencheur (ex: order.created, payment.completed)');
            $table->json('headers')->nullable()->comment('En-têtes HTTP personnalisés en JSON');
            $table->string('secret')->nullable()->comment('Secret pour signer les payloads');
            $table->boolean('is_active')->default(true)->comment('Indique si le webhook est actif');
            $table->integer('retry_count')->default(3)->comment('Nombre de tentatives de retry en cas d\'échec');
            $table->integer('timeout_seconds')->default(30)->comment('Délai d\'attente en secondes');
            $table->integer('success_count')->default(0)->comment('Nombre d\'appels réussis');
            $table->integer('failure_count')->default(0)->comment('Nombre d\'appels échoués');
            $table->datetime('last_triggered_at')->nullable()->comment('Date du dernier déclenchement');
            $table->foreignUuid('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['event', 'is_active']);
            $table->index(['is_active', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
