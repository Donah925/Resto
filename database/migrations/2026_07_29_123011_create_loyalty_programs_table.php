<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: loyalty_programs - Programmes de fidélité des restaurants
     */
    public function up(): void
    {
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->string('name')->comment('Nom du programme de fidélité');
            $table->text('description')->nullable()->comment('Description du programme');
            $table->string('type')->default('points')->comment('Type: points, stamp_card, tier_based');
            $table->integer('points_per_euro')->default(1)->comment('Nombre de points par euro dépensé');
            $table->integer('reward_threshold')->default(100)->comment('Seuil de points pour une récompense');
            $table->decimal('reward_value', 10, 2)->default(5)->comment('Valeur de la récompense en euros');
            $table->integer('stamp_goal')->nullable()->comment('Nombre de tampons pour une récompense (type stamp_card)');
            $table->json('tiers')->nullable()->comment('Configuration JSON des niveaux (type tier_based)');
            $table->boolean('is_active')->default(true)->comment('Indique si le programme est actif');
            $table->datetime('starts_at')->nullable()->comment('Date de début du programme');
            $table->datetime('ends_at')->nullable()->comment('Date de fin du programme');
            $table->timestamps();
            
            $table->index(['restaurant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_programs');
    }
};
