<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_addons', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'option supplémentaire');
            $table->uuid('product_id')->comment('Référence au produit parent');
            $table->string('nom', 100)->comment('Nom de l\'option supplémentaire');
            $table->text('description')->nullable()->comment('Description de l\'option');
            $table->decimal('prix', 10, 2)->default(0)->comment('Prix supplémentaire pour cette option');
            $table->boolean('est_obligatoire')->default(false)->comment('Indique si l\'option est obligatoire');
            $table->integer('quantite_max')->default(1)->comment('Quantité maximale pouvant être sélectionnée');
            $table->integer('ordre_affichage')->default(0)->comment('Ordre d\'affichage des options');
            $table->boolean('est_disponible')->default(true)->comment('Indique si l\'option est disponible');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->index('est_disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_addons');
    }
};
