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
        Schema::create('restaurant_images', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de l\'image');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->string('url')->comment('URL de l\'image');
            $table->string('type')->default('galerie')->comment('Type d\'image (logo, galerie, menu)');
            $table->string('alt')->nullable()->comment('Texte alternatif pour l\'accessibilité');
            $table->integer('ordre')->default(0)->comment('Ordre d\'affichage');
            $table->boolean('principale')->default(false)->comment('Image principale');
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->index('restaurant_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_images');
    }
};
