<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: delivery_drivers - Profils des livreurs
     */
    public function up(): void
    {
        Schema::create('delivery_drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('vehicle_type')->default('scooter')->comment('Type de véhicule: scooter, bike, car');
            $table->string('vehicle_license_plate')->nullable()->comment('Immatriculation du véhicule');
            $table->string('license_number')->nullable()->comment('Numéro de permis de conduire');
            $table->date('license_expiry')->nullable()->comment('Date d\'expiration du permis');
            $table->json('insurance_documents')->nullable()->comment('Documents d\'assurance en JSON');
            $table->boolean('is_verified')->default(false)->comment('Indique si le livreur est vérifié');
            $table->boolean('is_available')->default(true)->comment('Indique si le livreur est disponible');
            $table->decimal('current_latitude', 10, 8)->nullable()->comment('Latitude actuelle');
            $table->decimal('current_longitude', 11, 8)->nullable()->comment('Longitude actuelle');
            $table->decimal('total_deliveries', 10, 0)->default(0)->comment('Nombre total de livraisons effectuées');
            $table->decimal('average_rating', 3, 2)->default(0)->comment('Note moyenne du livreur');
            $table->timestamps();
            
            $table->index(['user_id', 'is_available']);
            $table->index(['is_verified', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_drivers');
    }
};
