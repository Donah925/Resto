<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: delivery_zones - Zones de livraison des restaurants
     */
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->string('name')->comment('Nom de la zone de livraison');
            $table->text('polygon_coordinates')->nullable()->comment('Coordonnées GeoJSON du polygone de la zone');
            $table->decimal('radius_km', 5, 2)->nullable()->comment('Rayon en km si zone circulaire');
            $table->decimal('center_latitude', 10, 8)->nullable()->comment('Latitude du centre');
            $table->decimal('center_longitude', 11, 8)->nullable()->comment('Longitude du centre');
            $table->decimal('delivery_fee', 8, 2)->default(0)->comment('Frais de livraison pour cette zone');
            $table->decimal('min_order_amount', 8, 2)->default(0)->comment('Montant minimum de commande pour cette zone');
            $table->integer('estimated_delivery_time_min')->default(30)->comment('Temps de livraison estimé en minutes');
            $table->boolean('is_active')->default(true)->comment('Indique si la zone est active');
            $table->timestamps();
            
            $table->index(['restaurant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
