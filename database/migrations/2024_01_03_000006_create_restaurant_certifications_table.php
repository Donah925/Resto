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
        Schema::create('restaurant_certifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la certification');
            $table->uuid('restaurant_id')->comment('Référence au restaurant');
            $table->string('nom')->comment('Nom de la certification');
            $table->string('organisme')->comment('Organisme certificateur');
            $table->date('date_obtention')->comment('Date d\'obtention');
            $table->date('date_expiration')->nullable()->comment('Date d\'expiration');
            $table->string('numero_certificat')->nullable()->comment('Numéro de certificat');
            $table->string('document_url')->nullable()->comment('URL du document');
            $table->boolean('visible')->default(true)->comment('Visible sur le profil');
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->index('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_certifications');
    }
};
