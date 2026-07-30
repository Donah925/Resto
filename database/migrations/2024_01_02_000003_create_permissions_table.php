<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identifiant unique de la permission');
            $table->string('name')->comment('Nom de la permission');
            $table->string('guard_name')->comment('Gardien d\'authentification');
            $table->timestamps();
            $table->unique(['name', 'guard_name'], 'permissions_name_guard_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
