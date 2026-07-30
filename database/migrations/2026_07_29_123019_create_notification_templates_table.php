<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: notification_templates - Modèles de notifications
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nom du modèle');
            $table->string('code')->unique()->comment('Code identifiant (ex: order_confirmed, reservation_reminder)');
            $table->string('type')->default('email')->comment('Type: email, sms, push_notification, in_app');
            $table->string('subject_template')->nullable()->comment('Modèle d\'objet avec variables {{variable}}');
            $table->text('body_template')->comment('Modèle de corps avec variables {{variable}}');
            $table->json('variables')->nullable()->comment('Liste JSON des variables disponibles');
            $table->boolean('is_active')->default(true)->comment('Indique si le modèle est actif');
            $table->string('language')->default('fr')->comment('Langue du modèle');
            $table->timestamps();
            
            $table->index(['code', 'type', 'language']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
