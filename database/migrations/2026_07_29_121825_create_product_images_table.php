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
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'image du produit');
            $table->uuid('product_id')->comment('Référence au produit associé');
            $table->string('url', 255)->comment('URL de l\'image');
            $table->string('alt_text', 150)->nullable()->comment('Texte alternatif pour l\'accessibilité');
            $table->boolean('est_principale')->default(false)->comment('Indique si c\'est l\'image principale du produit');
            $table->integer('ordre_affichage')->default(0)->comment('Ordre d\'affichage des images');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->index('est_principale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
