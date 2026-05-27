<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'name',
        'brand',
        'category',
        'quantity',
        'unit',
        'calories_per_100g',
        'proteins_per_100g',
        'carbs_per_100g',
        'fats_per_100g',
        'fiber_per_100g',
        'sugar_per_100g',
        'salt_per_100g',
        'saturated_fat_per_100g',
        'ingredients',
        'allergens',
        'nutriscore',
        'image_url',
        'open_food_facts_data'
    ];

    protected $appends = ['ean'];

    protected $casts = [
        'calories_per_100g' => 'decimal:2',
        'proteins_per_100g' => 'decimal:2',
        'carbs_per_100g' => 'decimal:2',
        'fats_per_100g' => 'decimal:2',
        'fiber_per_100g' => 'decimal:2',
        'sugar_per_100g' => 'decimal:2',
        'salt_per_100g' => 'decimal:2',
        'saturated_fat_per_100g' => 'decimal:2',
        'open_food_facts_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * EAN es alias de barcode para compatibilidad con la app móvil
     */
    public function getEanAttribute(): ?string
    {
        return $this->barcode;
    }

    /**
     * Relación con los items de despensa
     */
    public function pantryItems()
    {
        return $this->hasMany(PantryItem::class);
    }

    /**
     * Relación con los items de lista de compra
     */
    public function shoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    /**
     * Obtener información nutricional formateada
     */
    public function getNutritionalInfoAttribute(): array
    {
        return [
            'calories' => $this->calories_per_100g,
            'proteins' => $this->proteins_per_100g,
            'carbs' => $this->carbs_per_100g,
            'fats' => $this->fats_per_100g,
            'fiber' => $this->fiber_per_100g,
            'sugar' => $this->sugar_per_100g,
            'salt' => $this->salt_per_100g,
            'saturated_fat' => $this->saturated_fat_per_100g,
        ];
    }

    /**
     * Verificar si el producto es saludable (basado en nutriscore)
     */
    public function isHealthy(): bool
    {
        return in_array($this->nutriscore, ['a', 'b']);
    }

    /**
     * Obtener el color del nutriscore
     */
    public function getNutriscoreColorAttribute(): string
    {
        $colors = [
            'a' => '#008000', // Verde
            'b' => '#86b800', // Verde claro
            'c' => '#fecb00', // Amarillo
            'd' => '#f58220', // Naranja
            'e' => '#cc0000', // Rojo
        ];

        return $colors[$this->nutriscore] ?? '#cccccc'; // Gris por defecto
    }

    /**
     * Buscar productos por nombre o código de barras
     */
    public static function search(string $query)
    {
        return static::where('name', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%");
    }

    public static function normalizeCategory(string $raw): string
    {
        $text = strtolower($raw);

        $map = [
            'Lácteos'           => ['dairy', 'milk', 'cheese', 'yogurt', 'butter', 'cream', 'lacteo', 'leche', 'queso', 'yogur', 'mantequilla', 'nata'],
            'Carnes y pescados' => ['meat', 'fish', 'seafood', 'poultry', 'beef', 'pork', 'chicken', 'carne', 'pescado', 'marisco', 'pollo', 'cerdo', 'ternera'],
            'Frutas y verduras' => ['fruit', 'vegetable', 'produce', 'fruta', 'verdura', 'hortaliza', 'legumbre'],
            'Cereales y pasta'  => ['cereal', 'pasta', 'bread', 'flour', 'rice', 'grain', 'pan', 'harina', 'arroz', 'trigo', 'avena'],
            'Bebidas'           => ['beverage', 'drink', 'juice', 'water', 'soda', 'coffee', 'tea', 'bebida', 'zumo', 'agua', 'refresco', 'cafe'],
            'Conservas'         => ['canned', 'conserve', 'preserve', 'tinned', 'conserva', 'lata', 'enlatado'],
            'Congelados'        => ['frozen', 'congelado'],
            'Frutos secos'      => ['nut', 'seed', 'dried fruit', 'fruto seco', 'almendra', 'nuez', 'anacardo', 'semilla'],
            'Limpieza'          => ['cleaning', 'detergent', 'limpieza', 'detergente', 'soap'],
            'Higiene'           => ['hygiene', 'cosmetic', 'personal care', 'higiene', 'shampoo', 'toothpaste'],
        ];

        foreach ($map as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $category;
                }
            }
        }

        return 'Otros';
    }

    /**
     * Crear producto desde datos de UPC Item DB (sin info nutricional)
     */
    public static function createFromUpcItemDb(array $data): self
    {
        $product = new static();
        $product->barcode    = $data['code'];
        $product->name       = $data['product_name'] ?? 'Producto sin nombre';
        $product->brand      = $data['brands'] ?? null;
        $product->category   = static::normalizeCategory($data['category'] ?? '');
        $product->image_url  = $data['image_front_url'] ?? null;
        return $product;
    }

    /**
     * Crear producto desde datos de Open Food Facts
     */
    public static function createFromOpenFoodFacts(array $data): self
    {
        $product = new static();
        
        $product->barcode = $data['code'] ?? null;
        $product->name = $data['product_name'] ?? $data['product_name_es'] ?? 'Producto sin nombre';
        $product->brand = $data['brands'] ?? null;
        $product->category = static::normalizeCategory($data['categories'] ?? '');
        
        // Información nutricional
        $nutriments = $data['nutriments'] ?? [];
        $product->calories_per_100g = $nutriments['energy-kcal_100g'] ?? null;
        $product->proteins_per_100g = $nutriments['proteins_100g'] ?? null;
        $product->carbs_per_100g = $nutriments['carbohydrates_100g'] ?? null;
        $product->fats_per_100g = $nutriments['fat_100g'] ?? null;
        $product->fiber_per_100g = $nutriments['fiber_100g'] ?? null;
        $product->sugar_per_100g = $nutriments['sugars_100g'] ?? null;
        $product->salt_per_100g = $nutriments['salt_100g'] ?? null;
        $product->saturated_fat_per_100g = $nutriments['saturated-fat_100g'] ?? null;
        
        $product->ingredients = $data['ingredients_text'] ?? null;
        $product->allergens = $data['allergens'] ?? null;
        $nutriscore = $data['nutrition_grade_fr'] ?? null;
        $product->nutriscore = in_array($nutriscore, ['a', 'b', 'c', 'd', 'e']) ? $nutriscore : null;
        $product->image_url = $data['image_front_url'] ?? null;
        $product->open_food_facts_data = $data;

        return $product;
    }
}
