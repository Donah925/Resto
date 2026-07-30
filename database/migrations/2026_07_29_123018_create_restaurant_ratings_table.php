<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: restaurant_ratings - Notes agrégées des restaurants
     */
    public function up(): void
    {
        Schema::create('restaurant_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->unique()->constrained('restaurants')->onDelete('cascade');
            $table->decimal('overall_rating', 3, 2)->default(0)->comment('Note globale moyenne');
            $table->integer('total_reviews')->default(0)->comment('Nombre total d\'avis');
            $table->integer('rating_5_count')->default(0)->comment('Nombre d\'avis à 5 étoiles');
            $table->integer('rating_4_count')->default(0)->comment('Nombre d\'avis à 4 étoiles');
            $table->integer('rating_3_count')->default(0)->comment('Nombre d\'avis à 3 étoiles');
            $table->integer('rating_2_count')->default(0)->comment('Nombre d\'avis à 2 étoiles');
            $table->integer('rating_1_count')->default(0)->comment('Nombre d\'avis à 1 étoile');
            $table->decimal('food_rating', 3, 2)->default(0)->comment('Note moyenne pour la nourriture');
            $table->decimal('service_rating', 3, 2)->default(0)->comment('Note moyenne pour le service');
            $table->decimal('delivery_rating', 3, 2)->default(0)->comment('Note moyenne pour la livraison');
            $table->datetime('last_updated_at')->nullable()->comment('Date de dernière mise à jour');
            $table->timestamps();
            
            $table->index(['overall_rating', 'total_reviews']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_ratings');
    }
};
