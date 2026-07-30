<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: currencies - Devises supportées
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 3)->unique()->comment('Code ISO de la devise (ex: EUR, USD)');
            $table->string('name')->comment('Nom de la devise');
            $table->string('symbol')->comment('Symbole de la devise (ex: €, $)');
            $table->string('symbol_position')->default('after')->comment('Position du symbole: before, after');
            $table->integer('decimal_places')->default(2)->comment('Nombre de décimales');
            $table->decimal('exchange_rate', 12, 6)->default(1)->comment('Taux de change par rapport à la devise de base');
            $table->boolean('is_default')->default(false)->comment('Indique si c\'est la devise par défaut');
            $table->boolean('is_active')->default(true)->comment('Indique si la devise est active');
            $table->json('formatting_rules')->nullable()->comment('Règles JSON de formatage (séparateurs, etc.)');
            $table->timestamps();
            
            $table->index(['is_default', 'is_active']);
            $table->index(['is_active', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
