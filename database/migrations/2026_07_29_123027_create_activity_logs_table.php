<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: activity_logs - Journaux d'activité du système
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action')->comment('Action effectuée (ex: create, update, delete, login)');
            $table->string('model_type')->nullable()->comment('Type de modèle concerné (App\\Models\\Order, etc.)');
            $table->foreignUuid('model_id')->nullable()->comment('ID du modèle concerné');
            $table->json('old_values')->nullable()->comment('Anciennes valeurs en JSON avant modification');
            $table->json('new_values')->nullable()->comment('Nouvelles valeurs en JSON après modification');
            $table->string('ip_address')->nullable()->comment('Adresse IP de l\'utilisateur');
            $table->text('user_agent')->nullable()->comment('User agent du navigateur');
            $table->string('browser')->nullable()->comment('Navigateur utilisé');
            $table->string('os')->nullable()->comment('Système d\'exploitation');
            $table->string('device_type')->nullable()->comment('Type d\'appareil: desktop, mobile, tablet');
            $table->timestamps();
            
            $table->index(['user_id', 'action', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
