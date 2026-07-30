<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: user_notification_preferences - Préférences de notifications des utilisateurs
     */
    public function up(): void
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->boolean('email_order_updates')->default(true)->comment('Recevoir les mises à jour de commande par email');
            $table->boolean('sms_order_updates')->default(false)->comment('Recevoir les mises à jour de commande par SMS');
            $table->boolean('push_order_updates')->default(true)->comment('Recevoir les mises à jour de commande par push');
            $table->boolean('email_promotions')->default(true)->comment('Recevoir les promotions par email');
            $table->boolean('sms_promotions')->default(false)->comment('Recevoir les promotions par SMS');
            $table->boolean('push_promotions')->default(true)->comment('Recevoir les promotions par push');
            $table->boolean('email_reservation_reminders')->default(true)->comment('Recevoir les rappels de réservation par email');
            $table->boolean('sms_reservation_reminders')->default(true)->comment('Recevoir les rappels de réservation par SMS');
            $table->boolean('newsletter')->default(false)->comment('Inscription à la newsletter');
            $table->json('custom_preferences')->nullable()->comment('Préférences personnalisées en JSON');
            $table->timestamps();
            
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
