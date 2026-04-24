<?php

namespace App\Http\Controllers;

use App\Models\Pantry;
use App\Models\PantryItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PantryController extends Controller
{
    /**
     * Obtener despensas del usuario
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // Obtener despensas propias y compartidas
            $ownPantries = $user->pantries;
            $sharedPantries = $user->sharedPantries;

            $allPantries = $ownPantries->concat($sharedPantries);

            return response()->json([
                'success' => true,
                'message' => 'Pantries retrieved successfully',
                'data' => $allPantries->map(function ($pantry) use ($user) {
                    return [
                        'id' => $pantry->id,
                        'name' => $pantry->name,
                        'description' => $pantry->description,
                        'is_shared' => $pantry->is_shared,
                        'is_owner' => $pantry->user_id === $user->id,
                        'permission' => $pantry->user_id === $user->id ? 'admin' : 
                            $pantry->pivot->permission ?? 'read',
                        'items_count' => $pantry->items->count(),
                        'statistics' => $pantry->getStatistics(),
                        'created_at' => $pantry->created_at,
                        'updated_at' => $pantry->updated_at
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving pantries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva despensa
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
            $pantry = Pantry::create([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => $request->user()->id,
                'is_shared' => $request->is_shared ?? false,
                'shared_token' => $request->is_shared ? Pantry::generateSharedToken() : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pantry created successfully',
                'data' => $pantry
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating pantry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles de una despensa
     */
    public function show(Request $request, $id)
    {
        try {
            $pantry = Pantry::with(['items.product', 'sharedUsers'])->findOrFail($id);
            $user = $request->user();

            // Verificar permisos
            if (!$pantry->hasUserPermission($user, 'read')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Obtener items con estado
            $items = $pantry->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'expiry_date' => $item->expiry_date,
                    'location' => $item->location,
                    'notes' => $item->notes,
                    'minimum_quantity' => $item->minimum_quantity,
                    'status' => $item->status,
                    'status_color' => $item->status_color,
                    'days_until_expiry' => $item->days_until_expiry,
                    'is_low_stock' => $item->isLowStock(),
                    'nutritional_info' => $item->getTotalNutritionalInfo()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Pantry retrieved successfully',
                'data' => [
                    'id' => $pantry->id,
                    'name' => $pantry->name,
                    'description' => $pantry->description,
                    'is_shared' => $pantry->is_shared,
                    'shared_token' => $pantry->shared_token,
                    'is_owner' => $pantry->user_id === $user->id,
                    'permission' => $pantry->user_id === $user->id ? 'admin' : 
                        $pantry->pivot->permission ?? 'read',
                    'items' => $items,
                    'statistics' => $pantry->getStatistics(),
                    'expiring_soon' => $pantry->getExpiringSoonItems(),
                    'expired' => $pantry->getExpiredItems(),
                    'low_stock' => $pantry->getLowStockItems(),
                    'shared_users' => $pantry->sharedUsers,
                    'created_at' => $pantry->created_at,
                    'updated_at' => $pantry->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving pantry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una despensa compartida por token (ruta pública)
     */
    public function showSharedByToken($token)
    {
        try {
            $pantry = Pantry::with(['items.product', 'sharedUsers'])
                ->where('shared_token', $token)
                ->where('is_shared', true)
                ->firstOrFail();

            $items = $pantry->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'expiry_date' => $item->expiry_date,
                    'location' => $item->location,
                    'notes' => $item->notes,
                    'minimum_quantity' => $item->minimum_quantity,
                    'status' => $item->status,
                    'status_color' => $item->status_color,
                    'days_until_expiry' => $item->days_until_expiry,
                    'is_low_stock' => $item->isLowStock(),
                    'nutritional_info' => $item->getTotalNutritionalInfo()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Shared pantry retrieved successfully',
                'data' => [
                    'id' => $pantry->id,
                    'name' => $pantry->name,
                    'description' => $pantry->description,
                    'is_shared' => $pantry->is_shared,
                    'items' => $items,
                    'statistics' => $pantry->getStatistics(),
                    'shared_users' => $pantry->sharedUsers,
                    'created_at' => $pantry->created_at,
                    'updated_at' => $pantry->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving shared pantry: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Añadir item a despensa
     */
    public function addItem(Request $request, $pantryId)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'minimum_quantity' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pantry = Pantry::findOrFail($pantryId);
            $user = $request->user();

            // Verificar permisos
            if (!$pantry->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Verificar si el producto ya existe en la despensa
            $existingItem = $pantry->items()
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingItem) {
                // Actualizar cantidad existente
                $existingItem->quantity += $request->quantity;
                $existingItem->update($request->only([
                    'quantity', 'unit', 'expiry_date', 'location', 'notes', 'minimum_quantity'
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Item updated successfully',
                    'data' => $existingItem
                ]);
            } else {
                // Crear nuevo item
                $item = PantryItem::create([
                    'pantry_id' => $pantryId,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'unit' => $request->unit,
                    'expiry_date' => $request->expiry_date,
                    'location' => $request->location,
                    'notes' => $request->notes,
                    'minimum_quantity' => $request->minimum_quantity ?? 1
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
     * Actualizar item de despensa
     */
    public function updateItem(Request $request, $pantryId, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'minimum_quantity' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pantry = Pantry::findOrFail($pantryId);
            $user = $request->user();

            // Verificar permisos
            if (!$pantry->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $item = $pantry->items()->findOrFail($itemId);
            $item->update($request->only([
                'quantity', 'unit', 'expiry_date', 'location', 'notes', 'minimum_quantity'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar item de despensa
     */
    public function deleteItem(Request $request, $pantryId, $itemId)
    {
        try {
            $pantry = Pantry::findOrFail($pantryId);
            $user = $request->user();

            // Verificar permisos
            if (!$pantry->hasUserPermission($user, 'write')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $item = $pantry->items()->findOrFail($itemId);
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
     * Obtener notificaciones de despensa
     */
    public function notifications(Request $request, $id)
    {
        try {
            $pantry = Pantry::findOrFail($id);
            $user = $request->user();

            // Verificar permisos
            if (!$pantry->hasUserPermission($user, 'read')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $expiringSoon = $pantry->getExpiringSoonItems();
            $expired = $pantry->getExpiredItems();
            $lowStock = $pantry->getLowStockItems();

            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully',
                'data' => [
                    'expiring_soon' => $expiringSoon,
                    'expired' => $expired,
                    'low_stock' => $lowStock,
                    'total_alerts' => $expiringSoon->count() + $expired->count() + $lowStock->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compartir despensa
     */
    public function share(Request $request, $id)
    {
        try {
            $pantry = Pantry::findOrFail($id);
            $user = $request->user();

            // Solo el dueño puede compartir
            if ($pantry->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only owner can share pantry'
                ], 403);
            }

            $pantry->update([
                'is_shared' => true,
                'shared_token' => Pantry::generateSharedToken()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pantry shared successfully',
                'data' => [
                    'shared_token' => $pantry->shared_token,
                    'share_url' => url("/pantry/shared/{$pantry->shared_token}")
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sharing pantry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unirse a despensa compartida
     */
    public function joinShared(Request $request, $token)
    {
        try {
            $pantry = Pantry::where('shared_token', $token)
                ->where('is_shared', true)
                ->firstOrFail();

            $user = $request->user();

            // Verificar si ya es miembro
            if ($pantry->user_id === $user->id || 
                $pantry->sharedUsers()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already a member of this pantry'
                ], 400);
            }

            // Añadir usuario con permiso de lectura
            $pantry->sharedUsers()->attach($user->id, [
                'permission' => 'read'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Joined pantry successfully',
                'data' => $pantry
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error joining pantry: ' . $e->getMessage()
            ], 500);
        }
    }
}
