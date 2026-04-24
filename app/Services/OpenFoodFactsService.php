<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class OpenFoodFactsService
{
    /**
     * URL base de la API de Open Food Facts
     */
    private const BASE_URL = 'https://world.openfoodfacts.org/api/v0';

    /**
     * Obtener producto por código de barras
     */
    public function getProductByBarcode(string $barcode): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get(self::BASE_URL . "/product/{$barcode}.json");

            if (!$response->successful()) {
                Log::warning("Open Food Facts API error for barcode {$barcode}: " . $response->status());
                return null;
            }

            $data = $response->json();

            // Verificar si el producto existe
            if (!isset($data['status']) || $data['status'] !== 1) {
                Log::info("Product not found for barcode: {$barcode}");
                return null;
            }

            return $data['product'] ?? null;

        } catch (\Exception $e) {
            Log::error("Error fetching product from Open Food Facts: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Buscar productos por nombre
     */
    public function searchProducts(string $query, int $limit = 20): array
    {
        try {
            $response = Http::timeout(10)
                ->get(self::BASE_URL . "/search", [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'page_size' => $limit,
                    'json' => 1,
                    'fields' => 'code,product_name,brands,categories,nutriments,ingredients_text,allergens,nutrition_grade_fr,image_front_url'
                ]);

            if (!$response->successful()) {
                Log::warning("Open Food Facts search error for query {$query}: " . $response->status());
                return [];
            }

            $data = $response->json();
            
            return $data['products'] ?? [];

        } catch (\Exception $e) {
            Log::error("Error searching products in Open Food Facts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crear o actualizar producto desde Open Food Facts
     */
    public function createOrUpdateProduct(string $barcode): ?Product
    {
        $productData = $this->getProductByBarcode($barcode);
        
        if (!$productData) {
            return null;
        }

        // Verificar si el producto ya existe
        $product = Product::where('barcode', $barcode)->first();

        if ($product) {
            // Actualizar producto existente
            $this->updateProductFromApiData($product, $productData);
            return $product;
        } else {
            // Crear nuevo producto
            return $this->createProductFromApiData($productData);
        }
    }

    /**
     * Crear nuevo producto desde datos de la API
     */
    private function createProductFromApiData(array $data): Product
    {
        $product = Product::createFromOpenFoodFacts($data);
        $product->save();
        
        Log::info("Created new product from barcode: {$data['code']}");
        return $product;
    }

    /**
     * Actualizar producto existente con datos de la API
     */
    private function updateProductFromApiData(Product $product, array $data): void
    {
        $product->name = $data['product_name'] ?? $data['product_name_es'] ?? $product->name;
        $product->brand = $data['brands'] ?? $product->brand;
        $product->category = $data['categories'] ?? $product->category;
        
        // Actualizar información nutricional
        $nutriments = $data['nutriments'] ?? [];
        $product->calories_per_100g = $nutriments['energy-kcal_100g'] ?? $product->calories_per_100g;
        $product->proteins_per_100g = $nutriments['proteins_100g'] ?? $product->proteins_per_100g;
        $product->carbs_per_100g = $nutriments['carbohydrates_100g'] ?? $product->carbs_per_100g;
        $product->fats_per_100g = $nutriments['fat_100g'] ?? $product->fats_per_100g;
        $product->fiber_per_100g = $nutriments['fiber_100g'] ?? $product->fiber_per_100g;
        $product->sugar_per_100g = $nutriments['sugars_100g'] ?? $product->sugar_per_100g;
        $product->salt_per_100g = $nutriments['salt_100g'] ?? $product->salt_per_100g;
        $product->saturated_fat_per_100g = $nutriments['saturated-fat_100g'] ?? $product->saturated_fat_per_100g;
        
        $product->ingredients = $data['ingredients_text'] ?? $product->ingredients;
        $product->allergens = $data['allergens'] ?? $product->allergens;
        $product->nutriscore = $data['nutrition_grade_fr'] ?? $product->nutriscore;
        $product->image_url = $data['image_front_url'] ?? $product->image_url;
        $product->open_food_facts_data = $data;
        
        $product->save();
        
        Log::info("Updated product from barcode: {$data['code']}");
    }

    /**
     * Validar código de barras
     */
    public function isValidBarcode(string $barcode): bool
    {
        // Eliminar espacios y caracteres no numéricos
        $barcode = preg_replace('/[^0-9]/', '', $barcode);
        
        // Verificar longitud (EAN-13: 13 dígitos, UPC-A: 12 dígitos)
        return in_array(strlen($barcode), [8, 12, 13, 14]);
    }

    /**
     * Normalizar código de barras
     */
    public function normalizeBarcode(string $barcode): string
    {
        // Eliminar espacios y caracteres no numéricos
        return preg_replace('/[^0-9]/', '', $barcode);
    }

    /**
     * Obtener sugerencias basadas en productos similares
     */
    public function getProductSuggestions(string $barcode, int $limit = 5): array
    {
        $productData = $this->getProductByBarcode($barcode);
        
        if (!$productData) {
            return [];
        }

        $category = $productData['categories'] ?? '';
        $brand = $productData['brands'] ?? '';

        if (empty($category)) {
            return [];
        }

        return $this->searchProducts($category, $limit);
    }

    /**
     * Obtener información nutricional formateada
     */
    public function formatNutritionalInfo(array $productData): array
    {
        $nutriments = $productData['nutriments'] ?? [];

        return [
            'calories' => [
                'value' => $nutriments['energy-kcal_100g'] ?? 0,
                'unit' => 'kcal',
                'per_100g' => true
            ],
            'proteins' => [
                'value' => $nutriments['proteins_100g'] ?? 0,
                'unit' => 'g',
                'per_100g' => true
            ],
            'carbohydrates' => [
                'value' => $nutriments['carbohydrates_100g'] ?? 0,
                'unit' => 'g',
                'per_100g' => true
            ],
            'fats' => [
                'value' => $nutriments['fat_100g'] ?? 0,
                'unit' => 'g',
                'per_100g' => true
            ],
            'fiber' => [
                'value' => $nutriments['fiber_100g'] ?? 0,
                'unit' => 'g',
                'per_100g' => true
            ],
            'sugars' => [
                'value' => $nutriments['sugars_100g'] ?? 0,
                'unit' => 'g',
                'per_100g' => true
            ],
            'salt' => [
                'value' => $nutriments['salt_100g'] ?? 0,
                'unit' => 'g',
                'per_100g' => true
            ],
            'saturated_fat' => [
                'value' => $nutriments['saturated-fat_100g'] ?? 0,
                'unit' => 'g',
                'per_100g' => true
            ]
        ];
    }

    /**
     * Verificar si el producto es saludable
     */
    public function isProductHealthy(array $productData): bool
    {
        $nutriscore = $productData['nutrition_grade_fr'] ?? null;
        
        if (!$nutriscore) {
            return false;
        }

        return in_array(strtolower($nutriscore), ['a', 'b']);
    }

    /**
     * Obtener alérgenos comunes del producto
     */
    public function getAllergens(array $productData): array
    {
        $allergensText = $productData['allergens'] ?? '';
        
        if (empty($allergensText)) {
            return [];
        }

        // Lista de alérgenos comunes
        $commonAllergens = [
            'gluten', 'crustaceans', 'eggs', 'fish', 'peanuts',
            'soybeans', 'milk', 'nuts', 'celery', 'mustard',
            'sesame', 'sulphites', 'lupin', 'molluscs'
        ];

        $foundAllergens = [];
        
        foreach ($commonAllergens as $allergen) {
            if (stripos($allergensText, $allergen) !== false) {
                $foundAllergens[] = $allergen;
            }
        }

        return $foundAllergens;
    }
}
