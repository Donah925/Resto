<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: payment_transactions - Transactions de paiement
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignUuid('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->string('transaction_id')->unique()->comment('ID de transaction du fournisseur de paiement');
            $table->string('status')->default('pending')->comment('Statut: pending, success, failed, refunded, cancelled');
            $table->decimal('amount', 10, 2)->comment('Montant de la transaction');
            $table->string('currency')->default('EUR')->comment('Devise de la transaction');
            $table->json('payment_details')->nullable()->comment('Détails JSON de la réponse du paiement');
            $table->string('payment_gateway')->comment('Passerelle de paiement utilisée (ex: stripe, paypal)');
            $table->datetime('paid_at')->nullable()->comment('Date de paiement effectif');
            $table->datetime('refunded_at')->nullable()->comment('Date de remboursement');
            $table->text('failure_reason')->nullable()->comment('Raison de l\'échec du paiement');
            $table->integer('attempt_count')->default(1)->comment('Nombre de tentatives de paiement');
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index(['transaction_id']);
            $table->index(['payment_gateway', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
