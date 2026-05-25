<?php

namespace App\Services;

use App\Models\Pantry;
use App\Models\PantryItem;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ShoppingListService
{
    public function getUserLists(User $user): Collection
    {
        $own    = $user->shoppingLists()->with('items')->get();
        $shared = $user->sharedShoppingLists()->with('items')->get();

        return $own->concat($shared)->map(function (ShoppingList $list) use ($user) {
            $list->is_owner    = $list->user_id === $user->id;
            $list->items_count = $list->items->count();
            return $list;
        });
    }

    public function findForUser(int $id, User $user): ShoppingList
    {
        $list = ShoppingList::with(['items.product'])->findOrFail($id);

        if (!$list->hasUserPermission($user, 'read')) {
            abort(403, 'Access denied');
        }

        return $list;
    }

    public function create(array $data, User $user): ShoppingList
    {
        return ShoppingList::create([
            'name'         => $data['name'],
            'description'  => $data['description'] ?? null,
            'user_id'      => $user->id,
            'status'       => ShoppingList::STATUS_ACTIVE,
            'is_shared'    => $data['is_shared'] ?? false,
            'shared_token' => ($data['is_shared'] ?? false) ? ShoppingList::generateSharedToken() : null,
        ]);
    }

    public function update(int $id, array $data, User $user): ShoppingList
    {
        $list = ShoppingList::findOrFail($id);

        if ($list->user_id !== $user->id) {
            abort(403, 'Only the owner can update this list');
        }

        $list->update(array_filter([
            'name'        => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ], fn($v) => $v !== null));

        return $list->fresh();
    }

    public function delete(int $id, User $user): void
    {
        $list = ShoppingList::findOrFail($id);

        if ($list->user_id !== $user->id) {
            abort(403, 'Only the owner can delete this list');
        }

        $list->delete();
    }

    public function addItem(int $listId, array $data, User $user): ShoppingListItem
    {
        $list = ShoppingList::findOrFail($listId);

        if (!$list->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        if (!($data['product_id'] ?? null) && !($data['name'] ?? null)) {
            abort(422, 'product_id or name is required');
        }

        $existing = null;
        if ($data['product_id'] ?? null) {
            $existing = $list->items()->where('product_id', $data['product_id'])->first();
        } elseif ($data['name'] ?? null) {
            $existing = $list->items()->where('name', $data['name'])->whereNull('product_id')->first();
        }

        if ($existing) {
            $existing->quantity += $data['quantity'];
            $existing->update(array_intersect_key($data, array_flip(['unit', 'estimated_price', 'notes'])));
            return $existing->load('product');
        }

        return ShoppingListItem::create([
            'shopping_list_id' => $listId,
            'product_id'       => $data['product_id'] ?? null,
            'name'             => $data['name'] ?? null,
            'quantity'         => $data['quantity'],
            'unit'             => $data['unit'] ?? null,
            'estimated_price'  => $data['estimated_price'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'is_purchased'     => false,
        ])->load('product');
    }

    public function markPurchased(int $listId, int $itemId, User $user): ShoppingListItem
    {
        $list = ShoppingList::findOrFail($listId);

        if (!$list->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        $item = $list->items()->findOrFail($itemId);
        $item->markAsPurchased($user);
        return $item->load('product');
    }

    public function unmarkPurchased(int $listId, int $itemId, User $user): ShoppingListItem
    {
        $list = ShoppingList::findOrFail($listId);

        if (!$list->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        $item = $list->items()->findOrFail($itemId);
        $item->markAsNotPurchased();
        return $item->load('product');
    }

    public function deleteItem(int $listId, int $itemId, User $user): void
    {
        $list = ShoppingList::findOrFail($listId);

        if (!$list->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        $list->items()->findOrFail($itemId)->delete();
    }

    public function complete(int $id, User $user): ShoppingList
    {
        $list = ShoppingList::findOrFail($id);

        if (!$list->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied');
        }

        $list->markAsCompleted();
        return $list->fresh();
    }

    public function moveToPantry(int $listId, int $pantryId, User $user): array
    {
        $list   = ShoppingList::with(['items.product'])->findOrFail($listId);
        $pantry = Pantry::findOrFail($pantryId);

        if (!$list->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied to shopping list');
        }

        if (!$pantry->hasUserPermission($user, 'write')) {
            abort(403, 'Access denied to pantry');
        }

        $moved = [];

        foreach ($list->items()->where('is_purchased', true)->with('product')->get() as $item) {
            if (!$item->product_id) {
                continue;
            }

            $existing = $pantry->items()->where('product_id', $item->product_id)->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
                $moved[] = $existing->fresh();
            } else {
                $moved[] = PantryItem::create([
                    'pantry_id'  => $pantryId,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit'       => $item->unit,
                ]);
            }
        }

        return $moved;
    }

    public function formatItem(ShoppingListItem $item): array
    {
        $product = $item->product;
        return [
            'id'           => $item->id,
            'product_id'   => $item->product_id,
            'name'         => $item->name ?? ($product->name ?? null),
            'quantity'     => $item->quantity,
            'unit'         => $item->unit,
            'notes'        => $item->notes,
            'purchased'    => $item->is_purchased,
            'purchased_at' => $item->purchased_at,
            'product'      => $product ? [
                'id'       => $product->id,
                'name'     => $product->name,
                'brand'    => $product->brand,
                'category' => $product->category,
                'barcode'  => $product->barcode,
                'ean'      => $product->barcode,
            ] : null,
            'created_at'   => $item->created_at,
        ];
    }
}
