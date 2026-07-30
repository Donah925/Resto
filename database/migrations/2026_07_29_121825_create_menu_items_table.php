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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'élément du menu');
            $table->uuid('menu_id')->comment('Référence au menu parent');
            $table->uuid('product_id')->comment('Référence au produit associé');
            $table->integer('ordre_affichage')->default(0)->comment('Ordre d\'affichage dans le menu');
            $table->boolean('est_optionnel')->default(false)->comment('Indique si l\'élément est optionnel');
            $table->decimal('supplement_prix', 10, 2)->default(0)->comment('Supplément de prix pour cet élément');
            $table->text('notes')->nullable()->comment('Notes spéciales pour cet élément');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('menu_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
