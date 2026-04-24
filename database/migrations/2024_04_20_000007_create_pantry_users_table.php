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
        Schema::create('pantry_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pantry_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission', ['read', 'write', 'admin'])->default('read');
            $table->timestamps();
            
            // Índices únicos
            $table->unique(['pantry_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pantry_users');
    }
};
