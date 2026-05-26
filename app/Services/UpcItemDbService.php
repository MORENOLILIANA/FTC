<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpcItemDbService
{
    private const BASE_URL = 'https://api.upcitemdb.com/prod/trial/lookup';

    public function getProductByBarcode(string $barcode): ?array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders(['Accept' => 'application/json'])
                ->get(self::BASE_URL, ['upc' => $barcode]);

            if (!$response->successful()) {
                Log::warning("UPC Item DB error for barcode {$barcode}: " . $response->status());
                return null;
            }

            $data = $response->json();

            if (empty($data['items'])) {
                Log::info("Product not found in UPC Item DB for barcode: {$barcode}");
                return null;
            }

            $item = $data['items'][0];

            return [
                'code'            => $barcode,
                'product_name'    => $item['title'] ?? null,
                'brands'          => $item['brand'] ?? null,
                'category'        => $item['category'] ?? null,
                'image_front_url' => $item['images'][0] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error("Error fetching from UPC Item DB: " . $e->getMessage());
            return null;
        }
    }
}
