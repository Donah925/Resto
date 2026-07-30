<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: languages - Langues supportées
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 2)->unique()->comment('Code ISO de la langue (ex: fr, en)');
            $table->string('name')->comment('Nom de la langue');
            $table->string('native_name')->nullable()->comment('Nom natif de la langue');
            $table->string('locale')->comment('Locale complète (ex: fr_FR, en_US)');
            $table->string('direction')->default('ltr')->comment('Direction du texte: ltr, rtl');
            $table->string('flag_icon')->nullable()->comment('URL ou emoji du drapeau');
            $table->boolean('is_default')->default(false)->comment('Indique si c\'est la langue par défaut');
            $table->boolean('is_active')->default(true)->comment('Indique si la langue est active');
            $table->json('date_formats')->nullable()->comment('Formats JSON de date personnalisés');
            $table->integer('sort_order')->default(0)->comment('Ordre d\'affichage');
            $table->timestamps();
            
            $table->index(['is_default', 'is_active']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
