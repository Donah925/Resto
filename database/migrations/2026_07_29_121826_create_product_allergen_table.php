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
        Schema::create('product_allergen', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la relation produit-allergène');
            $table->uuid('product_id')->comment('Référence au produit');
            $table->uuid('allergen_id')->comment('Référence à l\'allergène');
            $table->boolean('est_trace')->default(false)->comment('Indique si c\'est une trace possible plutôt qu\'un ingrédient direct');
            $table->text('notes')->nullable()->comment('Notes supplémentaires sur la présence de l\'allergène');
            $table->timestamps();

            // Index et clés étrangères
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('allergen_id')->references('id')->on('allergens')->onDelete('cascade');
            $table->unique(['product_id', 'allergen_id']);
            $table->index('product_id');
            $table->index('allergen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_allergen');
    }
};
