<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: support_tickets - Tickets de support client
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->comment('Sujet du ticket');
            $table->text('message')->comment('Message initial');
            $table->string('status')->default('open')->comment('Statut: open, in_progress, waiting_customer, resolved, closed');
            $table->string('priority')->default('medium')->comment('Priorité: low, medium, high, urgent');
            $table->string('category')->comment('Catégorie: order_issue, payment_issue, technical, account, other');
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignUuid('restaurant_id')->nullable()->constrained('restaurants')->onDelete('set null');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->onDelete('set null')->comment('Agent support assigné');
            $table->datetime('resolved_at')->nullable()->comment('Date de résolution');
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Utilisateur ayant résolu le ticket');
            $table->integer('satisfaction_rating')->nullable()->comment('Note de satisfaction (1-5) après résolution');
            $table->text('satisfaction_comment')->nullable()->comment('Commentaire de satisfaction');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority', 'created_at']);
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
