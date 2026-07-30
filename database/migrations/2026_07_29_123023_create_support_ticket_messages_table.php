<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: support_ticket_messages - Messages des tickets de support
     */
    public function up(): void
    {
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('support_ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade')->comment('Utilisateur ayant envoyé le message');
            $table->text('message')->comment('Contenu du message');
            $table->json('attachments')->nullable()->comment('URLs des pièces jointes en JSON');
            $table->boolean('is_internal_note')->default(false)->comment('Indique si c\'est une note interne (non visible par le client)');
            $table->boolean('is_read')->default(false)->comment('Indique si le message a été lu');
            $table->datetime('read_at')->nullable()->comment('Date de lecture');
            $table->timestamps();
            
            $table->index(['support_ticket_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
    }
};
