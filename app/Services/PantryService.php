<?php

namespace App\Services;

use App\Models\Pantry;
use App\Models\PantryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class PantryService
{
    public function __construct(private ProductService $productService) {}
    public function getUserPantries(User $user): Collection
    {
        $own    = $user->pantries()->with('items')->get();
        $shared = $user->sharedPantries()->with('items')->get();

        return $own->concat($shared)->map(function (Pantry $pantry) use ($user) {
            $pantry->is_owner   = $pantry->user_id === $user->id;
            $pantry->items_count = $pantry->items->count();
            return $pantry;
        });
    }

    public function findForUser(int $id, User $user): Pantry
    {
        $pantry = Pantry::with(['items.product'])->findOrFail($id);

        if (!$pantry->hasUserPermission($user, 'read')) {
            abort(403, 'Access denied');
        }

        return $pantry;
    }

    public function create(array $data, User $user): Pantry
    {
        return Pantry::create([
            'name'         => $data['name'],
            'description'  => $data['description'] ?? null,
            'user_id'      => $user->id,
            'is_shared'    => $data['is_shared'] ?? false,
            'shared_token' => ($data['is_shared'] ?? false) ? Pantry::generateSharedToken() : null,
        ]);
    }

    public function update(int $id, array $data, User $user): Pantry
    {
        $pantry = Pantry::findOrFail($id);

        if ($pantry->user_id !== $user->id) {
            abort(403, 'Only the owner can update this pantry');
        }

        $pantry->update(array_filter([
            'name'        => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ], fn($v) => $v !== null));

        return $pantry->fresh();
    }

    public function delete(int $id, User $user): void
    {
        $pantry = Pantry::findOrFail($id);

        if ($pantry->user_id !== $user->id) {
            abort(403, 'Only the owner can delete this pantry');
        }

        $pantry->delete();
    }

    public function addItem(int $pantryId, array $data, User $user): PantryItem
    {
        $pantry = Pantry::findOrFail($pantryId);

        if (!$pantry->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        $productId = $data['product_id'] ?? null;

        // Buscar por barcode usando la cadena Open Food Facts → UPC Item DB → BD local
        if (!$productId && !empty($data['barcode'])) {
            $product   = $this->productService->getByBarcodeOrFetch($data['barcode']);
            $productId = $product?->id;
        }

        // Crear producto manualmente si no hay barcode ni product_id
        if (!$productId && isset($data['product_name'])) {
            $product   = Product::firstOrCreate(
                ['name' => $data['product_name'], 'brand' => $data['product_brand'] ?? null],
                ['category' => $data['product_category'] ?? null]
            );
            $productId = $product->id;
        }

        if (!$productId) {
            abort(422, 'Se requiere product_id, barcode o product_name');
        }

        $existing = $pantry->items()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->quantity += $data['quantity'];
            $existing->update(array_intersect_key($data, array_flip(['unit', 'expiry_date', 'location', 'notes', 'minimum_quantity'])));
            $existing->quantity = $existing->quantity; // already incremented
            $existing->save();
            return $existing->load('product');
        }

        return PantryItem::create([
            'pantry_id'        => $pantryId,
            'product_id'       => $productId,
            'quantity'         => $data['quantity'],
            'unit'             => $data['unit'] ?? null,
            'expiry_date'      => $data['expiry_date'] ?? null,
            'location'         => $data['location'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'minimum_quantity' => $data['minimum_quantity'] ?? 1,
        ])->load('product');
    }

    public function updateItem(int $pantryId, int $itemId, array $data, User $user): PantryItem
    {
        $pantry = Pantry::findOrFail($pantryId);

        if (!$pantry->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        $item = $pantry->items()->findOrFail($itemId);
        $item->update(array_intersect_key($data, array_flip([
            'quantity', 'unit', 'expiry_date', 'location', 'notes', 'minimum_quantity',
        ])));

        return $item->load('product');
    }

    public function deleteItem(int $pantryId, int $itemId, User $user): void
    {
        $pantry = Pantry::findOrFail($pantryId);

        if (!$pantry->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        $pantry->items()->findOrFail($itemId)->delete();
    }

    public function getNotifications(int $pantryId, User $user): array
    {
        $pantry = Pantry::findOrFail($pantryId);

        if (!$pantry->hasUserPermission($user, 'read')) {
            abort(403, 'Access denied');
        }

        $notifications = [];
        $id = 1;

        foreach ($pantry->getExpiredItems() as $item) {
            $notifications[] = [
                'id'         => $id++,
                'type'       => 'expired',
                'message'    => ($item->product->name ?? 'Producto') . ' ha caducado',
                'item_id'    => $item->id,
                'created_at' => $item->expiry_date,
            ];
        }

        foreach ($pantry->getExpiringSoonItems() as $item) {
            $notifications[] = [
                'id'         => $id++,
                'type'       => 'expiring_soon',
                'message'    => ($item->product->name ?? 'Producto') . ' caduca pronto',
                'item_id'    => $item->id,
                'created_at' => $item->expiry_date,
            ];
        }

        foreach ($pantry->getLowStockItems() as $item) {
            $notifications[] = [
                'id'         => $id++,
                'type'       => 'low_stock',
                'message'    => ($item->product->name ?? 'Producto') . ' tiene stock bajo',
                'item_id'    => $item->id,
                'created_at' => $item->updated_at,
            ];
        }

        return $notifications;
    }

    public function formatItem(PantryItem $item): array
    {
        $product = $item->product;
        return [
            'id'          => $item->id,
            'product_id'  => $item->product_id,
            'quantity'    => $item->quantity,
            'unit'        => $item->unit,
            'expiry_date' => $item->expiry_date,
            'location'    => $item->location,
            'notes'       => $item->notes,
            'product'     => $product ? [
                'id'       => $product->id,
                'name'     => $product->name,
                'brand'    => $product->brand,
                'category' => $product->category,
                'barcode'  => $product->barcode,
                'ean'      => $product->barcode,
            ] : null,
            'created_at' => $item->created_at,
        ];
    }
}
