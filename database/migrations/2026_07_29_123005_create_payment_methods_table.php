<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: payment_methods - Méthodes de paiement disponibles
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nom de la méthode de paiement');
            $table->string('code')->unique()->comment('Code identifiant (ex: card, paypal, cash)');
            $table->text('description')->nullable()->comment('Description de la méthode');
            $table->string('logo_url')->nullable()->comment('URL du logo de la méthode');
            $table->boolean('is_active')->default(true)->comment('Indique si la méthode est active');
            $table->boolean('is_online')->default(true)->comment('Indique si c\'est un paiement en ligne');
            $table->json('configuration')->nullable()->comment('Configuration JSON spécifique à la méthode');
            $table->integer('sort_order')->default(0)->comment('Ordre d\'affichage');
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
