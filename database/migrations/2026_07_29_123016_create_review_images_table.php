<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: review_images - Images des avis
     */
    public function up(): void
    {
        Schema::create('review_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('review_id')->constrained('reviews')->onDelete('cascade');
            $table->string('image_url')->comment('URL de l\'image');
            $table->string('thumbnail_url')->nullable()->comment('URL de la vignette');
            $table->integer('sort_order')->default(0)->comment('Ordre d\'affichage');
            $table->timestamps();
            
            $table->index(['review_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
