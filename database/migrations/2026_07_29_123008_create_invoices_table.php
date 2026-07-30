<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: invoices - Factures des commandes
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('invoice_number')->unique()->comment('Numéro de facture');
            $table->decimal('subtotal', 10, 2)->comment('Sous-total hors taxes');
            $table->decimal('tax_amount', 10, 2)->comment('Montant total des taxes');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Montant des réductions');
            $table->decimal('delivery_fee', 10, 2)->default(0)->comment('Frais de livraison');
            $table->decimal('total_amount', 10, 2)->comment('Montant total TTC');
            $table->string('tax_rate')->default('20')->comment('Taux de TVA appliqué en pourcentage');
            $table->json('tax_breakdown')->nullable()->comment('Détail JSON des taxes par taux');
            $table->text('billing_address')->comment('Adresse de facturation au format texte');
            $table->string('company_name')->nullable()->comment('Nom de l\'entreprise si facture pro');
            $table->string('vat_number')->nullable()->comment('Numéro de TVA intracommunautaire');
            $table->datetime('issued_at')->comment('Date d\'émission de la facture');
            $table->datetime('paid_at')->nullable()->comment('Date de paiement');
            $table->string('pdf_url')->nullable()->comment('URL du PDF de la facture');
            $table->timestamps();
            
            $table->index(['order_id']);
            $table->index(['invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
