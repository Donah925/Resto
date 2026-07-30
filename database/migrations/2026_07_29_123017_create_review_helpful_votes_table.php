<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: review_helpful_votes - Votes "utile" sur les avis
     */
    public function up(): void
    {
        Schema::create('review_helpful_votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('review_id')->constrained('reviews')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_helpful')->default(true)->comment('Indique si le vote est "utile" ou "pas utile"');
            $table->timestamps();
            
            $table->unique(['review_id', 'user_id']);
            $table->index(['review_id', 'is_helpful']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_helpful_votes');
    }
};
