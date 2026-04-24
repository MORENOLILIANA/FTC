<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Pantry;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
    /**
     * Obtener listas de compra del usuario
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // Obtener listas propias y compartidas
            $ownLists = $user->shoppingLists;
            $sharedLists = $user->sharedShoppingLists;

            $allLists = $ownLists->concat($sharedLists);

            return response()->json([
                'success' => true,
                'message' => 'Shopping lists retrieved successfully',
                'data' => $allLists->map(function ($list) use ($user) {
                    return [
                        'id' => $list->id,
                        'name' => $list->name,
                        'description' => $list->description,
                        'status' => $list->status,
                        'is_shared' => $list->is_shared,
                        'is_owner' => $list->user_id === $user->id,
                        'permission' => $list->user_id === $user->id ? 'admin' : 
                            $list->pivot->permission ?? 'read',
                        'items_count' => $list->items->count(),
                        'statistics' => $list->getStatistics(),
                        'created_at' => $list->created_at,
                        'updated_at' => $list->updated_at
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving shopping lists: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva lista de compra
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_shared' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $list = ShoppingList::create([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => $request->user()->id,
                'is_shared' => $request->is_shared ?? false,
                'shared_token' => $request->is_shared ? ShoppingList::generateSharedToken() : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shopping list created successfully',
                'data' => $list
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating shopping list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles de una lista de compra
     */
    public function show(Request $request, $id)
    {
        try {
            $list = ShoppingList::with(['items.product', 'sharedUsers'])->findOrFail($id);
            $user = $request->user();

            // Verificar permisos
            if (!$list->hasUserPermission($user, 'read')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Obtener items con estado
            $items = $list->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'estimated_price' => $item->estimated_price,
                    'total_estimated_price' => $item->getTotalEstimatedPrice(),
                    'notes' => $item->notes,
                    'is_purchased' => $item->is_purchased,
                    'purchased_at' => $item->purchased_at,
                    'purchased_by' => $item->purchasedBy,
                    'created_at' => $item->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Shopping list retrieved successfully',
                'data' => [
                    'id' => $list->id,
                    'name' => $list->name,
                    'description' => $list->description,
                    'status' => $list->status,
                    'is_shared' => $list->is_shared,
                    'shared_token' => $list->shared_token,
                    'is_owner' => $list->user_id === $user->id,
                    'permission' => $list->user_id === $user->id ? 'admin' : 
                        $list->pivot->permission ?? 'read',
                    'items' => $items,
                    'statistics' => $list->getStatistics(),
                    'pending_items' => $list->getPendingItems(),
                    'purchased_items' => $list->getPurchasedItems(),
                    'shared_users' => $list->sharedUsers,
                    'created_at' => $list->created_at,
                    'updated_at' => $list->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving shopping list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una lista compartida por token (ruta pública)
     */
    public function showSharedByToken($token)
    {
        try {
            $list = ShoppingList::with(['items.product', 'sharedUsers'])
                ->where('shared_token', $token)
                ->where('is_shared', true)
                ->firstOrFail();

            $items = $list->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'estimated_price' => $item->estimated_price,
                    'total_estimated_price' => $item->getTotalEstimatedPrice(),
                    'notes' => $item->notes,
                    'is_purchased' => $item->is_purchased,
                    'purchased_at' => $item->purchased_at,
                    'purchased_by' => $item->purchasedBy,
                    'created_at' => $item->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Shared shopping list retrieved successfully',
                'data' => [
                    'id' => $list->id,
                    'name' => $list->name,
                    'description' => $list->description,
                    'status' => $list->status,
                    'is_shared' => $list->is_shared,
                    'items' => $items,
                    'statistics' => $list->getStatistics(),
                    'shared_users' => $list->sharedUsers,
                    'created_at' => $list->created_at,
                    'updated_at' => $list->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving shared shopping list: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Añadir item a lista de compra
     */
    public function addItem(Request $request, $listId)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
            'estimated_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $list = ShoppingList::findOrFail($listId);
            $user = $request->user();

            // Verificar permisos
            if (!$list->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Verificar si el producto ya existe en la lista
            $existingItem = $list->items()
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingItem) {
                // Actualizar cantidad existente
                $existingItem->quantity += $request->quantity;
                $existingItem->update($request->only([
                    'quantity', 'unit', 'estimated_price', 'notes'
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Item updated successfully',
                    'data' => $existingItem
                ]);
            } else {
                // Crear nuevo item
                $item = ShoppingListItem::create([
                    'shopping_list_id' => $listId,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'unit' => $request->unit,
                    'estimated_price' => $request->estimated_price,
                    'notes' => $request->notes
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully',
                    'data' => $item
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar item como comprado
     */
    public function markItemPurchased(Request $request, $listId, $itemId)
    {
        try {
            $list = ShoppingList::findOrFail($listId);
            $user = $request->user();

            // Verificar permisos
            if (!$list->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $item = $list->items()->findOrFail($itemId);
            $item->markAsPurchased($user);

            return response()->json([
                'success' => true,
                'message' => 'Item marked as purchased',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking item as purchased: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desmarcar item como comprado
     */
    public function unmarkItemPurchased(Request $request, $listId, $itemId)
    {
        try {
            $list = ShoppingList::findOrFail($listId);
            $user = $request->user();

            // Verificar permisos
            if (!$list->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $item = $list->items()->findOrFail($itemId);
            $item->markAsNotPurchased();

            return response()->json([
                'success' => true,
                'message' => 'Item unmarked as purchased',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unmarking item as purchased: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar item de lista de compra
     */
    public function deleteItem(Request $request, $listId, $itemId)
    {
        try {
            $list = ShoppingList::findOrFail($listId);
            $user = $request->user();

            // Verificar permisos
            if (!$list->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $item = $list->items()->findOrFail($itemId);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Completar lista de compra
     */
    public function complete(Request $request, $id)
    {
        try {
            $list = ShoppingList::findOrFail($id);
            $user = $request->user();

            // Verificar permisos
            if (!$list->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $list->markAsCompleted();

            return response()->json([
                'success' => true,
                'message' => 'Shopping list completed successfully',
                'data' => $list
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing shopping list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener sugerencias de productos para lista
     */
    public function suggestions(Request $request, $id)
    {
        try {
            $list = ShoppingList::findOrFail($id);
            $user = $request->user();

            // Verificar permisos
            if (!$list->hasUserPermission($user, 'read')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Obtener despensas del usuario
            $pantries = $user->pantries;
            $suggestions = [];

            foreach ($pantries as $pantry) {
                $pantrySuggestions = $list->getSuggestedProducts($pantry);
                $suggestions = array_merge($suggestions, $pantrySuggestions->toArray());
            }

            // Eliminar duplicados y limitar
            $uniqueSuggestions = collect($suggestions)->unique('id')->take(10);

            return response()->json([
                'success' => true,
                'message' => 'Product suggestions retrieved successfully',
                'data' => $uniqueSuggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compartir lista de compra
     */
    public function share(Request $request, $id)
    {
        try {
            $list = ShoppingList::findOrFail($id);
            $user = $request->user();

            // Solo el dueño puede compartir
            if ($list->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only owner can share shopping list'
                ], 403);
            }

            $list->update([
                'is_shared' => true,
                'shared_token' => ShoppingList::generateSharedToken()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shopping list shared successfully',
                'data' => [
                    'shared_token' => $list->shared_token,
                    'share_url' => url("/shopping-list/shared/{$list->shared_token}")
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sharing shopping list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unirse a lista compartida
     */
    public function joinShared(Request $request, $token)
    {
        try {
            $list = ShoppingList::where('shared_token', $token)
                ->where('is_shared', true)
                ->firstOrFail();

            $user = $request->user();

            // Verificar si ya es miembro
            if ($list->user_id === $user->id || 
                $list->sharedUsers()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already a member of this shopping list'
                ], 400);
            }

            // Añadir usuario con permiso de lectura
            $list->sharedUsers()->attach($user->id, [
                'permission' => 'read'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Joined shopping list successfully',
                'data' => $list
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error joining shopping list: ' . $e->getMessage()
            ], 500);
        }
    }
}
