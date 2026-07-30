<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: promo_codes - Codes promotionnels
     */
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique()->comment('Code promotionnel à saisir');
            $table->string('name')->comment('Nom descriptif du code promo');
            $table->text('description')->nullable()->comment('Description détaillée');
            $table->string('type')->default('percentage')->comment('Type: percentage, fixed_amount, free_delivery');
            $table->decimal('value', 10, 2)->comment('Valeur de la réduction (pourcentage ou montant fixe)');
            $table->decimal('min_order_amount', 10, 2)->default(0)->comment('Montant minimum de commande requis');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->comment('Montant maximum de réduction (pour type percentage)');
            $table->datetime('valid_from')->comment('Date de début de validité');
            $table->datetime('valid_until')->comment('Date de fin de validité');
            $table->integer('usage_limit')->nullable()->comment('Nombre maximum d\'utilisations totales');
            $table->integer('usage_count')->default(0)->comment('Nombre d\'utilisations actuelles');
            $table->integer('per_user_limit')->default(1)->comment('Nombre maximum d\'utilisations par utilisateur');
            $table->boolean('is_active')->default(true)->comment('Indique si le code est actif');
            $table->json('applicable_restaurants')->nullable()->comment('IDs des restaurants applicables (null = tous)');
            $table->json('applicable_categories')->nullable()->comment('IDs des catégories applicables (null = toutes)');
            $table->foreignUuid('created_by')->constrained('users')->onDelete('restrict')->comment('Créateur du code promo');
            $table->timestamps();
            
            $table->index(['code', 'is_active']);
            $table->index(['is_active', 'valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
