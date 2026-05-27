<?php

namespace App\Http\Controllers;

use App\Services\PantryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PantryController extends Controller
{
    public function __construct(private PantryService $pantryService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $pantries = $this->pantryService->getUserPantries($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Pantries retrieved successfully',
                'data'    => $pantries,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_shared'   => 'boolean',
        ]);

        try {
            $pantry = $this->pantryService->create($request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Pantry created successfully',
                'data'    => $pantry,
            ], 201);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $pantry = $this->pantryService->findForUser($id, $request->user());

            $items = $pantry->items->map(fn($item) => $this->pantryService->formatItem($item));

            return response()->json([
                'success' => true,
                'message' => 'Pantry retrieved successfully',
                'data'    => [
                    'id'          => $pantry->id,
                    'name'        => $pantry->name,
                    'description' => $pantry->description,
                    'is_shared'   => $pantry->is_shared,
                    'is_owner'    => $pantry->user_id === $request->user()->id,
                    'items'       => $items,
                    'created_at'  => $pantry->created_at,
                    'updated_at'  => $pantry->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $pantry = $this->pantryService->update($id, $request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Pantry updated successfully',
                'data'    => $pantry,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $this->pantryService->delete($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Pantry deleted successfully',
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function addItem(Request $request, int $pantryId): JsonResponse
    {
        $request->validate([
            'product_id'                    => 'nullable|exists:products,id',
            'barcode'                       => 'nullable|string|max:14',
            'product_name'                  => 'nullable|string|max:255',
            'product_brand'                 => 'nullable|string|max:255',
            'product_category'              => 'nullable|string|max:255',
            'quantity'                      => 'required|numeric|min:0.01',
            'unit'                          => 'nullable|string|max:50',
            'expiry_date'                   => 'nullable|date',
            'location'                      => 'nullable|string|max:100',
            'notes'                         => 'nullable|string',
            'minimum_quantity'              => 'nullable|numeric|min:0',
            'nutritional_info'              => 'nullable|array',
            'nutritional_info.calories'     => 'nullable|numeric|min:0',
            'nutritional_info.proteins'     => 'nullable|numeric|min:0',
            'nutritional_info.carbs'        => 'nullable|numeric|min:0',
            'nutritional_info.fats'         => 'nullable|numeric|min:0',
            'nutritional_info.fiber'        => 'nullable|numeric|min:0',
            'nutritional_info.saturated_fat' => 'nullable|numeric|min:0',
            'nutritional_info.sugars'       => 'nullable|numeric|min:0',
            'nutritional_info.salt'         => 'nullable|numeric|min:0',
            'nutritional_info.nutriscore'   => 'nullable|string|max:1',
            'product_image_url'             => 'nullable|url',
        ]);

        try {
            $item = $this->pantryService->addItem($pantryId, $request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data'    => $this->pantryService->formatItem($item),
            ], 201);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function updateItem(Request $request, int $pantryId, int $itemId): JsonResponse
    {
        $request->validate([
            'quantity'                      => 'required|numeric|min:0.01',
            'unit'                          => 'nullable|string|max:50',
            'expiry_date'                   => 'nullable|date',
            'location'                      => 'nullable|string|max:100',
            'notes'                         => 'nullable|string',
            'minimum_quantity'              => 'nullable|numeric|min:0',
            'product_name'                  => 'nullable|string|max:255',
            'product_brand'                 => 'nullable|string|max:255',
            'product_category'              => 'nullable|string|max:255',
            'nutritional_info'              => 'nullable|array',
            'nutritional_info.calories'     => 'nullable|numeric|min:0',
            'nutritional_info.proteins'     => 'nullable|numeric|min:0',
            'nutritional_info.carbs'        => 'nullable|numeric|min:0',
            'nutritional_info.fats'         => 'nullable|numeric|min:0',
            'nutritional_info.fiber'        => 'nullable|numeric|min:0',
            'nutritional_info.saturated_fat' => 'nullable|numeric|min:0',
            'nutritional_info.sugars'       => 'nullable|numeric|min:0',
            'nutritional_info.salt'         => 'nullable|numeric|min:0',
            'nutritional_info.nutriscore'   => 'nullable|string|max:1',
            'product_image_url'             => 'nullable|url',
        ]);

        try {
            $item = $this->pantryService->updateItem($pantryId, $itemId, $request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => $this->pantryService->formatItem($item),
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function deleteItem(Request $request, int $pantryId, int $itemId): JsonResponse
    {
        try {
            $this->pantryService->deleteItem($pantryId, $itemId, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function notifications(Request $request, int $id): JsonResponse
    {
        try {
            $notifications = $this->pantryService->getNotifications($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully',
                'data'    => $notifications,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function members(Request $request, int $id): JsonResponse
    {
        try {
            $pantry = \App\Models\Pantry::findOrFail($id);

            if (!$pantry->hasUserPermission($request->user(), 'read')) {
                return response()->json(['success' => false, 'message' => 'Access denied'], 403);
            }

            $owner = $pantry->user()->select('id', 'name', 'email')->first();

            $members = collect([
                [
                    'id'         => $owner->id,
                    'name'       => $owner->name,
                    'email'      => $owner->email,
                    'role'       => 'owner',
                    'joined_at'  => $pantry->created_at,
                ],
            ]);

            $shared = $pantry->sharedUsers()
                ->select('users.id', 'users.name', 'users.email')
                ->get()
                ->map(fn($u) => [
                    'id'        => $u->id,
                    'name'      => $u->name,
                    'email'     => $u->email,
                    'role'      => $u->pivot->permission ?? 'member',
                    'joined_at' => $u->pivot->created_at,
                ]);

            return response()->json([
                'success' => true,
                'data'    => $members->concat($shared)->values(),
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    // ── shared access (public routes) ──────────────────────────────────────

    public function share(Request $request, int $id): JsonResponse
    {
        try {
            $pantry = \App\Models\Pantry::findOrFail($id);

            if ($pantry->user_id !== $request->user()->id) {
                return response()->json(['success' => false, 'message' => 'Only owner can share pantry'], 403);
            }

            $pantry->update(['is_shared' => true, 'shared_token' => \App\Models\Pantry::generateSharedToken()]);

            return response()->json([
                'success' => true,
                'message' => 'Pantry shared successfully',
                'data'    => ['shared_token' => $pantry->shared_token],
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function joinShared(Request $request, string $token): JsonResponse
    {
        try {
            $pantry = \App\Models\Pantry::where('shared_token', $token)->where('is_shared', true)->firstOrFail();
            $user   = $request->user();

            if ($pantry->user_id === $user->id || $pantry->sharedUsers()->where('user_id', $user->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Already a member'], 400);
            }

            $pantry->sharedUsers()->attach($user->id, ['permission' => 'write']);

            return response()->json(['success' => true, 'message' => 'Joined pantry successfully', 'data' => $pantry]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function showSharedByToken(string $token): JsonResponse
    {
        try {
            $pantry = \App\Models\Pantry::with(['items.product'])->where('shared_token', $token)->where('is_shared', true)->firstOrFail();
            $items  = $pantry->items->map(fn($item) => $this->pantryService->formatItem($item));

            return response()->json([
                'success' => true,
                'message' => 'Shared pantry retrieved successfully',
                'data'    => ['id' => $pantry->id, 'name' => $pantry->name, 'items' => $items],
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    // ── helper ─────────────────────────────────────────────────────────────

    private function error(\Exception $e): JsonResponse
    {
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
    }
}
