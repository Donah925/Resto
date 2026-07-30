<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: system_settings - Paramètres du système
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique()->comment('Clé du paramètre (ex: site_name, maintenance_mode)');
            $table->text('value')->nullable()->comment('Valeur du paramètre');
            $table->string('type')->default('string')->comment('Type de donnée: string, boolean, integer, float, json, array');
            $table->string('group')->default('general')->comment('Groupe de paramétrage: general, email, payment, delivery, etc.');
            $table->text('description')->nullable()->comment('Description du paramètre');
            $table->json('meta')->nullable()->comment('Métadonnées JSON (options, validation rules, etc.)');
            $table->boolean('is_public')->default(false)->comment('Indique si le paramètre est accessible publiquement');
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['group', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
