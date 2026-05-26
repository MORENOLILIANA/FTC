<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        private OpenFoodFactsService $openFoodFacts,
        private UpcItemDbService $upcItemDb,
    ) {}

    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::query();

        if ($filters['category'] ?? null) {
            $query->where('category', 'like', '%' . $filters['category'] . '%');
        }
        if ($filters['brand'] ?? null) {
            $query->where('brand', 'like', '%' . $filters['brand'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function getByBarcodeOrFetch(string $barcode): ?Product
    {
        $normalized = $this->openFoodFacts->normalizeBarcode($barcode);

        // 1. Base de datos local
        $product = Product::where('barcode', $normalized)->first();
        if ($product) {
            Log::info("Barcode {$normalized} found in local DB");
            return $product;
        }

        // 2. Open Food Facts
        $product = $this->openFoodFacts->createOrUpdateProduct($normalized);
        if ($product) {
            Log::info("Barcode {$normalized} found in Open Food Facts");
            return $product;
        }

        // 3. UPC Item DB (fallback, sin datos nutricionales)
        $upcData = $this->upcItemDb->getProductByBarcode($normalized);
        if ($upcData) {
            Log::info("Barcode {$normalized} found in UPC Item DB");
            $product = Product::createFromUpcItemDb($upcData);
            $product->save();
            return $product;
        }

        return null;
    }

    public function getSuggestions(string $barcode): array
    {
        $normalized = $this->openFoodFacts->normalizeBarcode($barcode);
        return $this->openFoodFacts->getProductSuggestions($normalized);
    }

    public function search(string $query, int $limit = 20)
    {
        $local = Product::search($query)->take($limit)->get();

        if ($local->count() >= $limit) {
            return $local;
        }

        $apiResults = $this->openFoodFacts->searchProducts($query, $limit - $local->count());
        $fromApi    = collect($apiResults)->map(fn($d) => Product::createFromOpenFoodFacts($d));

        return $local->concat($fromApi)->take($limit);
    }

    public function create(array $data): Product
    {
        return Product::create([
            'barcode'  => $data['barcode'] ?? null,
            'name'     => $data['name'],
            'brand'    => $data['brand'] ?? null,
            'category' => $data['category'] ?? null,
            'unit'     => $data['unit'] ?? null,
        ]);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);

        $product->update(array_filter([
            'name'     => $data['name'] ?? null,
            'brand'    => $data['brand'] ?? null,
            'category' => $data['category'] ?? null,
            'barcode'  => $data['barcode'] ?? null,
            'unit'     => $data['unit'] ?? null,
        ], fn($v) => $v !== null));

        return $product->fresh();
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }
}
