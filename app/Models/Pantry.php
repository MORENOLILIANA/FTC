<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pantry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_shared',
        'shared_token'
    ];

    protected $casts = [
        'is_shared' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el usuario dueño
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con los items de la despensa
     */
    public function items()
    {
        return $this->hasMany(PantryItem::class);
    }

    /**
     * Relación con usuarios compartidos
     */
    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'pantry_users')
            ->withPivot('permission')
            ->withTimestamps();
    }

    /**
     * Obtener items próximos a caducar
     */
    public function getExpiringSoonItems($days = 7)
    {
        return $this->items()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Obtener items caducados
     */
    public function getExpiredItems()
    {
        return $this->items()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->orderBy('expiry_date', 'desc')
            ->get();
    }

    /**
     * Obtener items bajos en stock
     */
    public function getLowStockItems($threshold = 2)
    {
        return $this->items()
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity', 'asc')
            ->get();
    }

    /**
     * Verificar si el usuario tiene permiso para acceder a la despensa
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
     * Generar token para compartir despensa
     */
    public static function generateSharedToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Obtener estadísticas de la despensa
     */
    public function getStatistics(): array
    {
        $items = $this->items;
        
        return [
            'total_items' => $items->count(),
            'total_value' => $items->sum(function ($item) {
                return $item->quantity * ($item->product->calories_per_100g ?? 0);
            }),
            'expiring_soon' => $this->getExpiringSoonItems()->count(),
            'expired' => $this->getExpiredItems()->count(),
            'low_stock' => $this->getLowStockItems()->count(),
            'categories' => $items->groupBy('product.category')->map->count(),
        ];
    }

    /**
     * Obtener productos únicos para sugerencias de recetas
     */
    public function getUniqueProducts()
    {
        return $this->items()
            ->with('product')
            ->get()
            ->pluck('product')
            ->unique('id');
    }
}
