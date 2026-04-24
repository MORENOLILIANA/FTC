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
        Schema::create('pantry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pantry_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('location')->nullable(); // Ej: nevera, alacena, despensa
            $table->text('notes')->nullable();
            $table->decimal('minimum_quantity', 10, 2)->default(1); // Cantidad mínima para alerta
            $table->timestamps();
            
            // Índices para mejor rendimiento
            $table->index('pantry_id');
            $table->index('product_id');
            $table->index('expiry_date');
            $table->unique(['pantry_id', 'product_id']); // Un producto por despensa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pantry_items');
    }
};
