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
        Schema::create('role_has_roles', function (Blueprint $table) {
            $table->uuid('parent_id');
            $table->uuid('child_id');
            $table->primary(['parent_id', 'child_id']);
            $table->foreign('parent_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_roles');
    }
};
