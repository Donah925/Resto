<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: refunds - Remboursements des commandes
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignUuid('payment_transaction_id')->constrained('payment_transactions')->onDelete('restrict');
            $table->decimal('amount', 10, 2)->comment('Montant remboursé');
            $table->string('reason')->comment('Raison du remboursement');
            $table->text('notes')->nullable()->comment('Notes additionnelles sur le remboursement');
            $table->string('status')->default('pending')->comment('Statut: pending, approved, rejected, processed');
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Utilisateur ayant approuvé le remboursement');
            $table->datetime('approved_at')->nullable()->comment('Date d\'approbation');
            $table->datetime('processed_at')->nullable()->comment('Date de traitement effectif');
            $table->string('refund_transaction_id')->nullable()->unique()->comment('ID de transaction de remboursement');
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
