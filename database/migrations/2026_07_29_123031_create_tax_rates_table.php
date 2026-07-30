<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: tax_rates - Taux de taxe configurables
     */
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nom de la taxe (ex: TVA 20%, Taxe service)');
            $table->decimal('rate', 5, 2)->comment('Taux de taxe en pourcentage');
            $table->string('type')->default('percentage')->comment('Type: percentage, fixed');
            $table->decimal('fixed_amount', 10, 2)->nullable()->comment('Montant fixe si type = fixed');
            $table->boolean('is_compound')->default(false)->comment('Indique si la taxe est cumulative');
            $table->boolean('is_active')->default(true)->comment('Indique si la taxe est active');
            $table->string('applies_to')->default('all')->comment('Cible: all, products, delivery, service_fee');
            $table->json('applicable_categories')->nullable()->comment('IDs des catégories applicables (null = toutes)');
            $table->foreignUuid('restaurant_id')->nullable()->constrained('restaurants')->onDelete('cascade')->comment('Restaurant associé (null = global)');
            $table->datetime('valid_from')->nullable()->comment('Date de début de validité');
            $table->datetime('valid_until')->nullable()->comment('Date de fin de validité');
            $table->timestamps();
            
            $table->index(['is_active', 'applies_to']);
            $table->index(['restaurant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
