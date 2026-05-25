<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
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
            $table->char('nutriscore', 1)->nullable();
            $table->string('image_url')->nullable();
            $table->text('open_food_facts_data')->nullable();
            $table->timestamps();
            $table->index('barcode');
            $table->index('name');
            $table->index('category');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
