<?php

namespace App\Http\Controllers;

use App\Services\ShoppingListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function __construct(private ShoppingListService $listService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $lists = $this->listService->getUserLists($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Shopping lists retrieved successfully',
                'data'    => $lists->map(fn($l) => [
                    'id'           => $l->id,
                    'name'         => $l->name,
                    'status'       => $l->status,
                    'items_count'  => $l->items_count,
                    'created_at'   => $l->created_at,
                    'updated_at'   => $l->updated_at,
                    'completed_at' => $l->completed_at,
                ]),
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
            $list = $this->listService->create($request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Shopping list created successfully',
                'data'    => $list,
            ], 201);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $list  = $this->listService->findForUser($id, $request->user());
            $items = $list->items->map(fn($item) => $this->listService->formatItem($item));

            return response()->json([
                'success' => true,
                'message' => 'Shopping list retrieved successfully',
                'data'    => [
                    'id'           => $list->id,
                    'name'         => $list->name,
                    'status'       => $list->status,
                    'is_shared'    => $list->is_shared,
                    'is_owner'     => $list->user_id === $request->user()->id,
                    'items'        => $items,
                    'created_at'   => $list->created_at,
                    'updated_at'   => $list->updated_at,
                    'completed_at' => $list->completed_at,
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
            $list = $this->listService->update($id, $request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Shopping list updated successfully',
                'data'    => $list,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $this->listService->delete($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Shopping list deleted successfully',
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function addItem(Request $request, int $listId): JsonResponse
    {
        $request->validate([
            'product_id'      => 'nullable|exists:products,id',
            'name'            => 'nullable|string|max:255',
            'quantity'        => 'required|numeric|min:0.01',
            'unit'            => 'nullable|string|max:50',
            'estimated_price' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        try {
            $item = $this->listService->addItem($listId, $request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data'    => $this->listService->formatItem($item),
            ], 201);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function markItemPurchased(Request $request, int $listId, int $itemId): JsonResponse
    {
        try {
            $item = $this->listService->markPurchased($listId, $itemId, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Item marked as purchased',
                'data'    => $this->listService->formatItem($item),
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function unmarkItemPurchased(Request $request, int $listId, int $itemId): JsonResponse
    {
        try {
            $item = $this->listService->unmarkPurchased($listId, $itemId, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Item unmarked as purchased',
                'data'    => $this->listService->formatItem($item),
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function deleteItem(Request $request, int $listId, int $itemId): JsonResponse
    {
        try {
            $this->listService->deleteItem($listId, $itemId, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function moveToPantry(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'pantry_id' => 'required|integer|exists:pantries,id',
        ]);

        try {
            $moved = $this->listService->moveToPantry($id, $request->integer('pantry_id'), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Purchased items moved to pantry successfully',
                'data'    => ['moved_items' => count($moved)],
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $list = $this->listService->complete($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Shopping list completed successfully',
                'data'    => $list,
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    // ── shared access (public routes) ──────────────────────────────────────

    public function suggestions(Request $request, int $id): JsonResponse
    {
        try {
            $list      = $this->listService->findForUser($id, $request->user());
            $pantries  = $request->user()->pantries;
            $suggested = [];

            foreach ($pantries as $pantry) {
                $suggested = array_merge($suggested, $list->getSuggestedProducts($pantry)->toArray());
            }

            return response()->json([
                'success' => true,
                'message' => 'Suggestions retrieved successfully',
                'data'    => collect($suggested)->unique('id')->take(10)->values(),
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function share(Request $request, int $id): JsonResponse
    {
        try {
            $list = \App\Models\ShoppingList::findOrFail($id);

            if ($list->user_id !== $request->user()->id) {
                return response()->json(['success' => false, 'message' => 'Only owner can share'], 403);
            }

            $list->update(['is_shared' => true, 'shared_token' => \App\Models\ShoppingList::generateSharedToken()]);

            return response()->json([
                'success' => true,
                'message' => 'Shopping list shared successfully',
                'data'    => ['shared_token' => $list->shared_token],
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function joinShared(Request $request, string $token): JsonResponse
    {
        try {
            $list = \App\Models\ShoppingList::where('shared_token', $token)->where('is_shared', true)->firstOrFail();
            $user = $request->user();

            if ($list->user_id === $user->id || $list->sharedUsers()->where('user_id', $user->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Already a member'], 400);
            }

            $list->sharedUsers()->attach($user->id, ['permission' => 'read']);

            return response()->json(['success' => true, 'message' => 'Joined list successfully', 'data' => $list]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function showSharedByToken(string $token): JsonResponse
    {
        try {
            $list  = \App\Models\ShoppingList::with(['items.product'])->where('shared_token', $token)->where('is_shared', true)->firstOrFail();
            $items = $list->items->map(fn($item) => $this->listService->formatItem($item));

            return response()->json([
                'success' => true,
                'message' => 'Shared shopping list retrieved successfully',
                'data'    => ['id' => $list->id, 'name' => $list->name, 'status' => $list->status, 'items' => $items],
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
