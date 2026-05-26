<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'category' => 'string',
            'brand'    => 'string',
        ]);

        try {
            $products = $this->productService->getAll($request->only('category', 'brand'), $request->input('per_page', 20));

            return response()->json([
                'success'    => true,
                'message'    => 'Products retrieved successfully',
                'data'       => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page'     => $products->perPage(),
                    'total'        => $products->total(),
                    'last_page'    => $products->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'brand'    => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'barcode'  => 'nullable|string|max:14|unique:products,barcode',
            'unit'     => 'nullable|string|max:50',
        ]);

        try {
            $product = $this->productService->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data'    => $product,
            ], 201);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Product found',
                'data'    => $this->productService->findById($id),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'brand'    => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'barcode'  => 'nullable|string|max:14|unique:products,barcode,' . $id,
            'unit'     => 'nullable|string|max:50',
        ]);

        try {
            $product = $this->productService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data'    => $product,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function getByBarcode(string $barcode): JsonResponse
    {
        try {
            $product = $this->productService->getByBarcodeOrFetch($barcode);

            if (!$product) {
                return response()->json([
                    'success'            => false,
                    'message'            => 'Producto no encontrado en ninguna base de datos',
                    'needs_manual_entry' => true,
                    'barcode'            => $barcode,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product found',
                'data'    => $product,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function suggestions(string $barcode): JsonResponse
    {
        try {
            $suggestions = $this->productService->getSuggestions($barcode);

            return response()->json([
                'success' => true,
                'message' => 'Product suggestions found',
                'data'    => $suggestions,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['query' => 'required|string|min:2', 'limit' => 'integer|min:1|max:50']);

        try {
            $results = $this->productService->search($request->input('query'), $request->input('limit', 20));

            return response()->json([
                'success' => true,
                'message' => 'Products found',
                'data'    => $results,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function nutritionalInfo(string $barcode): JsonResponse
    {
        try {
            $product = \App\Models\Product::where('barcode', $barcode)->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => 'Nutritional information found',
                'data'    => [
                    'product'          => $product,
                    'nutritional_info' => $product->nutritional_info,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
    }

    private function error(\Exception $e): JsonResponse
    {
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
    }
}
