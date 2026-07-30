<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: loyalty_point_transactions - Transactions de points de fidélité
     */
    public function up(): void
    {
        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_loyalty_point_id')->constrained('user_loyalty_points')->onDelete('cascade');
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('type')->comment('Type: earned, redeemed, expired, adjusted');
            $table->integer('points')->comment('Nombre de points (positif ou négatif)');
            $table->integer('balance_after')->comment('Solde après la transaction');
            $table->text('description')->nullable()->comment('Description de la transaction');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Utilisateur ayant créé l\'ajustement');
            $table->timestamps();
            
            $table->index(['user_loyalty_point_id', 'type']);
            $table->index(['order_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
    }
};
