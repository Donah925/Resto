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
        Schema::create('user_favorite_restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du favori');
            $table->uuid('user_id')->comment('Référence à l\'utilisateur');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'restaurant_id'], 'unique_favorite');
            $table->index('user_id');
            $table->index('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favorite_restaurants');
    }
};
