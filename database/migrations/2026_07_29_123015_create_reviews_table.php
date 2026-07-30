<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: reviews - Avis des utilisateurs sur les restaurants
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->onDelete('set null')->comment('Commande associée à l\'avis');
            $table->integer('rating')->comment('Note de 1 à 5');
            $table->text('comment')->nullable()->comment('Commentaire détaillé');
            $table->json('ratings_breakdown')->nullable()->comment('Détail JSON des notes par critère (nourriture, service, livraison)');
            $table->boolean('is_verified_purchase')->default(false)->comment('Indique si l\'avis provient d\'une commande vérifiée');
            $table->boolean('is_visible')->default(true)->comment('Indique si l\'avis est visible publiquement');
            $table->boolean('is_featured')->default(false)->comment('Indique si l\'avis est mis en avant');
            $table->integer('helpful_count')->default(0)->comment('Nombre de votes "utile"');
            $table->foreignUuid('response_by')->nullable()->constrained('users')->onDelete('set null')->comment('Utilisateur ayant répondu (gérant)');
            $table->text('response')->nullable()->comment('Réponse du restaurant');
            $table->datetime('responded_at')->nullable()->comment('Date de la réponse');
            $table->timestamps();
            
            $table->index(['restaurant_id', 'rating']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_visible', 'is_featured', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
