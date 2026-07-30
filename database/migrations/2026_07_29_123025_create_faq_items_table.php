<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: faq_items - Éléments de FAQ (questions/réponses)
     */
    public function up(): void
    {
        Schema::create('faq_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('faq_category_id')->constrained('faq_categories')->onDelete('cascade');
            $table->string('question')->comment('Question de la FAQ');
            $table->text('answer')->comment('Réponse détaillée');
            $table->json('related_links')->nullable()->comment('Liens connexes en JSON');
            $table->integer('view_count')->default(0)->comment('Nombre de consultations');
            $table->integer('helpful_count')->default(0)->comment('Nombre de votes "utile"');
            $table->integer('not_helpful_count')->default(0)->comment('Nombre de votes "pas utile"');
            $table->boolean('is_active')->default(true)->comment('Indique si l\'élément est actif');
            $table->integer('sort_order')->default(0)->comment('Ordre d\'affichage dans la catégorie');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['faq_category_id', 'is_active', 'sort_order']);
            $table->fullText(['question', 'answer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_items');
    }
};
