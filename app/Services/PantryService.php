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
        $pantry = Pantry::with(['items.product', 'items.user'])->findOrFail($id);

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

        // Actualizar datos nutricionales e imagen en el producto si se enviaron
        if ($productId && (!empty($data['nutritional_info']) || !empty($data['product_image_url']))) {
            $this->applyNutritionalData(Product::find($productId), $data);
        }

        if (!$productId) {
            abort(422, 'Se requiere product_id, barcode o product_name');
        }

        $existing = $pantry->items()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->quantity += $data['quantity'];
            $existing->update(array_intersect_key($data, array_flip(['unit', 'expiry_date', 'location', 'notes', 'minimum_quantity'])));
            $existing->quantity = $existing->quantity;
            if (!$existing->user_id) {
                $existing->user_id = $user->id;
            }
            $existing->save();
            return $existing->load('product', 'user');
        }

        return PantryItem::create([
            'pantry_id'        => $pantryId,
            'product_id'       => $productId,
            'user_id'          => $user->id,
            'quantity'         => $data['quantity'],
            'unit'             => $data['unit'] ?? null,
            'expiry_date'      => $data['expiry_date'] ?? null,
            'location'         => $data['location'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'minimum_quantity' => $data['minimum_quantity'] ?? 1,
        ])->load('product', 'user');
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

        if ($item->product) {
            $productUpdate = array_filter([
                'name'     => $data['product_name'] ?? null,
                'brand'    => $data['product_brand'] ?? null,
                'category' => $data['product_category'] ?? null,
            ], fn($v) => $v !== null);

            if (!empty($productUpdate)) {
                $item->product->update($productUpdate);
            }

            $this->applyNutritionalData($item->product, $data);
        }

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
                'id'            => $product->id,
                'name'          => $product->name,
                'brand'         => $product->brand,
                'category'      => $product->category,
                'barcode'       => $product->barcode,
                'ean'           => $product->barcode,
                'image_url'     => $product->image_url ?: null,
                'nutriscore'            => $product->nutriscore,
                'calories'              => $product->calories_per_100g,
                'proteins'              => $product->proteins_per_100g,
                'carbohydrates'         => $product->carbs_per_100g,
                'fats'                  => $product->fats_per_100g,
                'saturated_fat_per_100g'=> $product->saturated_fat_per_100g,
                'fiber'                 => $product->fiber_per_100g,
                'sugar'                 => $product->sugar_per_100g,
                'salt'                  => $product->salt_per_100g,
            ] : null,
            'added_by'    => $item->user ? [
                'id'   => $item->user->id,
                'name' => $item->user->name,
            ] : null,
            'created_at' => $item->created_at,
        ];
    }

    private function applyNutritionalData(?Product $product, array $data): void
    {
        if (!$product) return;

        $update = [];
        $info   = $data['nutritional_info'] ?? [];

        $map = [
            'calories'      => 'calories_per_100g',
            'proteins'      => 'proteins_per_100g',
            'carbs'         => 'carbs_per_100g',
            'fats'          => 'fats_per_100g',
            'saturated_fat' => 'saturated_fat_per_100g',
            'fiber'         => 'fiber_per_100g',
            'sugars'        => 'sugar_per_100g',
            'salt'          => 'salt_per_100g',
            'nutriscore'    => 'nutriscore',
        ];

        foreach ($map as $fromKey => $toColumn) {
            if (isset($info[$fromKey])) {
                $update[$toColumn] = $info[$fromKey];
            }
        }

        if (!empty($data['product_image_url']) && empty($product->image_url)) {
            $update['image_url'] = $data['product_image_url'];
        }

        if (!empty($update)) {
            $product->update($update);
            $product->refresh();
        }

        // Calcular nutriscore automáticamente si hay datos pero no se envió uno explícito
        if (empty($info['nutriscore']) && empty($product->nutriscore) && $product->calories_per_100g !== null) {
            $ns = $this->calculateNutriscore($product);
            if ($ns) $product->update(['nutriscore' => $ns]);
        }
    }

    private function calculateNutriscore(Product $product): ?string
    {
        $cal  = (float) $product->calories_per_100g;
        $sat  = (float) $product->saturated_fat_per_100g;
        $sug  = (float) $product->sugar_per_100g;
        $salt = (float) $product->salt_per_100g;
        $fib  = (float) $product->fiber_per_100g;
        $prot = (float) $product->proteins_per_100g;

        if ($cal === 0.0 && $sat === 0.0 && $sug === 0.0) return null;

        $n = $this->nsEnergyPts($cal) + $this->nsSatFatPts($sat)
           + $this->nsSugarPts($sug) + $this->nsSodiumPts($salt * 400);

        $isFruitVeg = in_array($product->category, ['Frutas y verduras', 'Legumbres']);
        $p = $this->nsFiberPts($fib) + $this->nsProteinPts($prot) + ($isFruitVeg ? 5 : 0);

        return match (true) {
            ($n - $p) <= -1 => 'A',
            ($n - $p) <= 2  => 'B',
            ($n - $p) <= 10 => 'C',
            ($n - $p) <= 18 => 'D',
            default         => 'E',
        };
    }

    private function nsEnergyPts(float $v): int
    {
        return match(true) {
            $v<=335=>0,$v<=670=>1,$v<=1005=>2,$v<=1340=>3,$v<=1675=>4,
            $v<=2010=>5,$v<=2345=>6,$v<=2680=>7,$v<=3015=>8,$v<=3350=>9,default=>10
        };
    }

    private function nsSatFatPts(float $v): int
    {
        return match(true) {
            $v<=1=>0,$v<=2=>1,$v<=3=>2,$v<=4=>3,$v<=5=>4,
            $v<=6=>5,$v<=7=>6,$v<=8=>7,$v<=9=>8,$v<=10=>9,default=>10
        };
    }

    private function nsSugarPts(float $v): int
    {
        return match(true) {
            $v<=4.5=>0,$v<=9=>1,$v<=13.5=>2,$v<=18=>3,$v<=22.5=>4,
            $v<=27=>5,$v<=31=>6,$v<=36=>7,$v<=40=>8,$v<=45=>9,default=>10
        };
    }

    private function nsSodiumPts(float $v): int
    {
        return match(true) {
            $v<=90=>0,$v<=180=>1,$v<=270=>2,$v<=360=>3,$v<=450=>4,
            $v<=540=>5,$v<=630=>6,$v<=720=>7,$v<=810=>8,$v<=900=>9,default=>10
        };
    }

    private function nsFiberPts(float $v): int
    {
        return match(true) {
            $v<=0.9=>0,$v<=1.9=>1,$v<=2.8=>2,$v<=3.7=>3,$v<=4.7=>4,default=>5
        };
    }

    private function nsProteinPts(float $v): int
    {
        return match(true) {
            $v<=1.6=>0,$v<=3.2=>1,$v<=4.8=>2,$v<=6.4=>3,$v<=8.0=>4,default=>5
        };
    }
}
