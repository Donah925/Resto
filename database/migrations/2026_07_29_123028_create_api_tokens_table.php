<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: api_tokens - Tokens d'API pour l'authentification
     */
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->comment('Nom descriptif du token');
            $table->string('token', 64)->unique()->comment('Token hashé');
            $table->json('abilities')->nullable()->comment('Permissions JSON associées au token');
            $table->datetime('last_used_at')->nullable()->comment('Date de dernière utilisation');
            $table->datetime('expires_at')->nullable()->comment('Date d\'expiration du token');
            $table->string('ip_address')->nullable()->comment('Dernière adresse IP utilisée');
            $table->boolean('is_revoked')->default(false)->comment('Indique si le token est révoqué');
            $table->timestamps();
            
            $table->index(['user_id', 'is_revoked']);
            $table->index(['token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
