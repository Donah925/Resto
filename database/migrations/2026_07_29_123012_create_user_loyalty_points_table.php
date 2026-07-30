<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: user_loyalty_points - Points de fidélité des utilisateurs
     */
    public function up(): void
    {
        Schema::create('user_loyalty_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->foreignUuid('loyalty_program_id')->constrained('loyalty_programs')->onDelete('cascade');
            $table->integer('points_balance')->default(0)->comment('Solde actuel de points');
            $table->integer('lifetime_points')->default(0)->comment('Total de points accumulés depuis le début');
            $table->integer('stamps_count')->default(0)->comment('Nombre de tampons actuels (type stamp_card)');
            $table->string('current_tier')->nullable()->comment('Niveau actuel (type tier_based)');
            $table->datetime('last_activity_at')->nullable()->comment('Date de la dernière activité');
            $table->timestamps();
            
            $table->unique(['user_id', 'restaurant_id', 'loyalty_program_id']);
            $table->index(['user_id', 'points_balance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_loyalty_points');
    }
};
