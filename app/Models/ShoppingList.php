<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_shared',
        'shared_token',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'is_shared' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Relación con el usuario dueño
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con los items de la lista
     */
    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    /**
     * Relación con usuarios compartidos
     */
    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'shopping_list_users')
            ->withPivot('permission')
            ->withTimestamps();
    }

    /**
     * Obtener items pendientes
     */
    public function getPendingItems()
    {
        return $this->items()->where('is_purchased', false)->get();
    }

    /**
     * Obtener items comprados
     */
    public function getPurchasedItems()
    {
        return $this->items()->where('is_purchased', true)->get();
    }

    /**
     * Verificar si el usuario tiene permiso para acceder a la lista
     */
    public function hasUserPermission(User $user, string $permission = 'read'): bool
    {
        // El dueño tiene todos los permisos
        if ($this->user_id === $user->id) {
            return true;
        }

        // Verificar permisos compartidos
        $sharedUser = $this->sharedUsers()
            ->where('user_id', $user->id)
            ->first();

        if (!$sharedUser) {
            return false;
        }

        $permissions = [
            'read' => ['read', 'write', 'admin'],
            'write' => ['write', 'admin'],
            'admin' => ['admin']
        ];

        return in_array($sharedUser->pivot->permission, $permissions[$permission] ?? []);
    }

    /**
     * Generar token para compartir lista
     */
    public static function generateSharedToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Obtener estadísticas de la lista
     */
    public function getStatistics(): array
    {
        $items = $this->items;
        
        return [
            'total_items' => $items->count(),
            'pending_items' => $items->where('is_purchased', false)->count(),
            'purchased_items' => $items->where('is_purchased', true)->count(),
            'estimated_cost' => $items->sum(function ($item) {
                return $item->quantity * ($item->estimated_price ?? 0);
            }),
            'completion_percentage' => $items->count() > 0 
                ? round(($items->where('is_purchased', true)->count() / $items->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Marcar lista como completada
     */
    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Obtener productos sugeridos basados en la despensa
     */
    public function getSuggestedProducts(Pantry $pantry)
    {
        $pantryProducts = $pantry->getUniqueProducts()->pluck('id');
        $listProducts = $this->items()->pluck('product_id');
        
        // Sugerir productos que están en la despensa pero no en la lista
        return Product::whereIn('id', $pantryProducts)
            ->whereNotIn('id', $listProducts)
            ->limit(10)
            ->get();
    }
}
