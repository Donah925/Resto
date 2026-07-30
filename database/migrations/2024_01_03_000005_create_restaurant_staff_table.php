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
        Schema::create('restaurant_staff', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique du membre du personnel');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->uuid('user_id')->comment('Référence à l\'utilisateur');
            $table->enum('role', ['manager', 'serveur', 'cuisinier', 'barman', 'autre'])->comment('Rôle dans le restaurant');
            $table->date('date_embauche')->comment('Date d\'embauche');
            $table->date('date_depart')->nullable()->comment('Date de départ');
            $table->boolean('actif')->default(true)->comment('Membre actif');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['restaurant_id', 'user_id'], 'unique_staff_member');
            $table->index('restaurant_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_staff');
    }
};
