<?php

namespace App\Services;

use App\Models\User;

class NotificationService
{
    public function getForUser(User $user, int $days = 7): array
    {
        $notifications = [];
        $id = 1;

        $pantries = $user->pantries()->with(['items.product'])->get()
            ->concat($user->sharedPantries()->with(['items.product'])->get());

        foreach ($pantries as $pantry) {
            foreach ($pantry->getExpiredItems() as $item) {
                $notifications[] = [
                    'id'         => $id++,
                    'type'       => 'expired',
                    'pantry_id'  => $pantry->id,
                    'pantry'     => $pantry->name,
                    'product'    => $item->product->name ?? 'Unknown product',
                    'message'    => ($item->product->name ?? 'Product') . ' has expired',
                    'item_id'    => $item->id,
                    'expiry_date' => $item->expiry_date,
                    'created_at' => now(),
                ];
            }

            foreach ($pantry->getExpiringSoonItems($days) as $item) {
                $notifications[] = [
                    'id'          => $id++,
                    'type'        => 'expiring_soon',
                    'pantry_id'   => $pantry->id,
                    'pantry'      => $pantry->name,
                    'product'     => $item->product->name ?? 'Unknown product',
                    'message'     => ($item->product->name ?? 'Product') . ' expires soon',
                    'item_id'     => $item->id,
                    'expiry_date' => $item->expiry_date,
                    'created_at'  => now(),
                ];
            }

            foreach ($pantry->getLowStockItems() as $item) {
                $notifications[] = [
                    'id'        => $id++,
                    'type'      => 'low_stock',
                    'pantry_id' => $pantry->id,
                    'pantry'    => $pantry->name,
                    'product'   => $item->product->name ?? 'Unknown product',
                    'message'   => ($item->product->name ?? 'Product') . ' is running low',
                    'item_id'   => $item->id,
                    'quantity'  => $item->quantity,
                    'created_at' => now(),
                ];
            }
        }

        return $notifications;
    }
}
