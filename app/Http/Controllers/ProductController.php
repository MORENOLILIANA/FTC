<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\OpenFoodFactsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $openFoodFactsService;

    public function __construct(OpenFoodFactsService $openFoodFactsService)
    {
        $this->openFoodFactsService = $openFoodFactsService;
    }

    /**
     * Obtener producto por código de barras
     */
    public function getByBarcode($barcode)
    {
        $validator = Validator::make(['barcode' => $barcode], [
            'barcode' => 'required|string|min:8|max:14'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode format',
                'errors' => $validator->errors()
            ], 422);
        }

        // Normalizar código de barras
        $normalizedBarcode = $this->openFoodFactsService->normalizeBarcode($barcode);

        if (!$this->openFoodFactsService->isValidBarcode($normalizedBarcode)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode format'
            ], 400);
        }

        try {
            // Buscar en base de datos primero
            $product = Product::where('barcode', $normalizedBarcode)->first();

            if ($product) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product found in database',
                    'data' => $product,
                    'source' => 'database'
                ]);
            }

            // Si no está en base de datos, buscar en Open Food Facts
            $productData = $this->openFoodFactsService->getProductByBarcode($normalizedBarcode);

            if (!$productData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Crear producto desde datos de la API
            $product = $this->openFoodFactsService->createOrUpdateProduct($normalizedBarcode);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product found and created',
                'data' => $product,
                'source' => 'open_food_facts'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar productos
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $request->query;
        $limit = $request->limit ?? 20;

        try {
            // Buscar en base de datos primero
            $databaseProducts = Product::search($query)->take($limit)->get();

            // Si hay suficientes resultados, devolverlos
            if ($databaseProducts->count() >= $limit) {
                return response()->json([
                    'success' => true,
                    'message' => 'Products found in database',
                    'data' => $databaseProducts,
                    'source' => 'database'
                ]);
            }

            // Complementar con búsqueda en Open Food Facts
            $remainingLimit = $limit - $databaseProducts->count();
            $apiProducts = $this->openFoodFactsService->searchProducts($query, $remainingLimit);

            // Convertir productos de API a modelos (sin guardar)
            $formattedApiProducts = collect($apiProducts)->map(function ($productData) {
                return Product::createFromOpenFoodFacts($productData);
            });

            // Combinar resultados
            $allProducts = $databaseProducts->concat($formattedApiProducts);

            return response()->json([
                'success' => true,
                'message' => 'Products found',
                'data' => $allProducts->take($limit),
                'database_count' => $databaseProducts->count(),
                'api_count' => $formattedApiProducts->count(),
                'total_count' => $allProducts->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener sugerencias de productos
     */
    public function suggestions($barcode)
    {
        $validator = Validator::make(['barcode' => $barcode], [
            'barcode' => 'required|string|min:8|max:14'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode format'
            ], 422);
        }

        $normalizedBarcode = $this->openFoodFactsService->normalizeBarcode($barcode);

        try {
            $suggestions = $this->openFoodFactsService->getProductSuggestions($normalizedBarcode);

            return response()->json([
                'success' => true,
                'message' => 'Product suggestions found',
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información nutricional detallada
     */
    public function nutritionalInfo($barcode)
    {
        $validator = Validator::make(['barcode' => $barcode], [
            'barcode' => 'required|string|min:8|max:14'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode format'
            ], 422);
        }

        $normalizedBarcode = $this->openFoodFactsService->normalizeBarcode($barcode);

        try {
            $product = Product::where('barcode', $normalizedBarcode)->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Obtener información nutricional formateada
            $nutritionalInfo = $this->openFoodFactsService->formatNutritionalInfo(
                $product->open_food_facts_data ?? []
            );

            // Obtener alérgenos
            $allergens = $this->openFoodFactsService->getAllergens(
                $product->open_food_facts_data ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Nutritional information found',
                'data' => [
                    'product' => $product,
                    'nutritional_info' => $nutritionalInfo,
                    'allergens' => $allergens,
                    'is_healthy' => $product->isHealthy(),
                    'nutriscore_color' => $product->nutriscore_color
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting nutritional info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los productos (paginado)
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'category' => 'string',
            'brand' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = Product::query();

            // Filtros
            if ($request->category) {
                $query->where('category', 'like', "%{$request->category}%");
            }

            if ($request->brand) {
                $query->where('brand', 'like', "%{$request->brand}%");
            }

            // Paginación
            $perPage = $request->per_page ?? 20;
            $products = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener producto por ID
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Product found',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }
}
