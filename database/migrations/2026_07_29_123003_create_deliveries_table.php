<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: deliveries - Livraisons des commandes
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignUuid('delivery_address_id')->constrained('delivery_addresses')->onDelete('restrict');
            $table->foreignUuid('driver_id')->nullable()->constrained('users')->onDelete('set null')->comment('Livreur assigné');
            $table->string('status')->default('pending')->comment('Statut: pending, assigned, picked_up, in_transit, delivered, failed');
            $table->datetime('picked_up_at')->nullable()->comment('Heure de récupération de la commande');
            $table->datetime('delivered_at')->nullable()->comment('Heure de livraison effective');
            $table->decimal('delivery_fee', 8, 2)->comment('Frais de livraison appliqués');
            $table->decimal('tip_amount', 8, 2)->default(0)->comment('Pourboire donné au livreur');
            $table->text('delivery_instructions')->nullable()->comment('Instructions spécifiques pour la livraison');
            $table->text('driver_notes')->nullable()->comment('Notes du livreur');
            $table->json('tracking_history')->nullable()->comment('Historique JSON du suivi de livraison');
            $table->decimal('current_latitude', 10, 8)->nullable()->comment('Latitude actuelle du livreur');
            $table->decimal('current_longitude', 11, 8)->nullable()->comment('Longitude actuelle du livreur');
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index(['driver_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
