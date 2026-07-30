<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: faq_categories - Catégories de FAQ
     */
    public function up(): void
    {
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nom de la catégorie');
            $table->text('description')->nullable()->comment('Description de la catégorie');
            $table->string('slug')->unique()->comment('Slug URL de la catégorie');
            $table->integer('sort_order')->default(0)->comment('Ordre d\'affichage');
            $table->boolean('is_active')->default(true)->comment('Indique si la catégorie est active');
            $table->json('meta_data')->nullable()->comment('Métadonnées JSON additionnelles');
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_categories');
    }
};
