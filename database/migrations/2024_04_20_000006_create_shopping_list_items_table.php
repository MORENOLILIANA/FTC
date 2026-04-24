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
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->nullable();
            $table->decimal('estimated_price', 10, 2)->nullable(); // Precio estimado por unidad
            $table->text('notes')->nullable();
            $table->boolean('is_purchased')->default(false);
            $table->timestamp('purchased_at')->nullable();
            $table->foreignId('purchased_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Índices para mejor rendimiento
            $table->index('shopping_list_id');
            $table->index('product_id');
            $table->index('is_purchased');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};
