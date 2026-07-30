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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la catégorie');
            $table->uuid('parent_id')->nullable()->comment('Référence à la catégorie parent pour les catégories imbriquées');
            $table->string('nom', 100)->comment('Nom de la catégorie');
            $table->text('description')->nullable()->comment('Description détaillée de la catégorie');
            $table->string('slug', 150)->unique()->comment('URL slug unique pour la catégorie');
            $table->string('image_url', 255)->nullable()->comment('URL de l\'image de la catégorie');
            $table->integer('ordre')->default(0)->comment('Ordre d\'affichage des catégories');
            $table->boolean('est_actif')->default(true)->comment('Indique si la catégorie est active et visible');
            $table->json('meta_donnees')->nullable()->comment('Métadonnées supplémentaires au format JSON');
            $table->timestamps();
            $table->softDeletes();

            // Index et clés étrangères
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            $table->index('slug');
            $table->index('est_actif');
            $table->index('ordre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
