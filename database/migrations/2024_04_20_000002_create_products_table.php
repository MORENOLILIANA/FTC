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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique()->nullable();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable();
            
            // Información nutricional (por 100g)
            $table->decimal('calories_per_100g', 8, 2)->nullable();
            $table->decimal('proteins_per_100g', 8, 2)->nullable();
            $table->decimal('carbs_per_100g', 8, 2)->nullable();
            $table->decimal('fats_per_100g', 8, 2)->nullable();
            $table->decimal('fiber_per_100g', 8, 2)->nullable();
            $table->decimal('sugar_per_100g', 8, 2)->nullable();
            $table->decimal('salt_per_100g', 8, 2)->nullable();
            $table->decimal('saturated_fat_per_100g', 8, 2)->nullable();
            
            $table->text('ingredients')->nullable();
            $table->text('allergens')->nullable();
            $table->char('nutriscore', 1)->nullable(); // A, B, C, D, E
            $table->string('image_url')->nullable();
            $table->json('open_food_facts_data')->nullable(); // Datos originales de la API
            
            $table->timestamps();
            
            // Índices para mejor rendimiento
            $table->index('barcode');
            $table->index('name');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
