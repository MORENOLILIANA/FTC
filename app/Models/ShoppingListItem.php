<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopping_list_id',
        'product_id',
        'name',
        'quantity',
        'unit',
        'estimated_price',
        'notes',
        'is_purchased',
        'purchased_at',
        'purchased_by_user_id'
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'is_purchased' => 'boolean',
        'purchased_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con la lista de compra
     */
    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    /**
     * Relación con el producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación con el usuario que compró
     */
    public function purchasedBy()
    {
        return $this->belongsTo(User::class, 'purchased_by_user_id');
    }

    /**
     * Marcar como comprado
     */
    public function markAsPurchased(User $user = null)
    {
        $this->is_purchased = true;
        $this->purchased_at = now();
        $this->purchased_by_user_id = $user?->id;
        $this->save();
    }

    /**
     * Marcar como no comprado
     */
    public function markAsNotPurchased()
    {
        $this->is_purchased = false;
        $this->purchased_at = null;
        $this->purchased_by_user_id = null;
        $this->save();
    }

    /**
     * Verificar si el item está disponible en la despensa
     */
    public function isAvailableInPantry(Pantry $pantry): bool
    {
        return $pantry->items()
            ->where('product_id', $this->product_id)
            ->where('quantity', '>', 0)
            ->exists();
    }

    /**
     * Obtener cantidad disponible en despensa
     */
    public function getAvailableQuantityInPantry(Pantry $pantry): float
    {
        $pantryItem = $pantry->items()
            ->where('product_id', $this->product_id)
            ->first();

        return $pantryItem ? $pantryItem->quantity : 0;
    }

    /**
     * Scope para items pendientes
     */
    public function scopePending($query)
    {
        return $query->where('is_purchased', false);
    }

    /**
     * Scope para items comprados
     */
    public function scopePurchased($query)
    {
        return $query->where('is_purchased', true);
    }

    /**
     * Obtener costo total estimado
     */
    public function getTotalEstimatedPrice(): float
    {
        return $this->quantity * ($this->estimated_price ?? 0);
    }

    /**
     * Verificar si necesita ser comprado urgentemente
     */
    public function isUrgent(Pantry $pantry): bool
    {
        // Es urgente si no está disponible en la despensa
        // o si la cantidad en despensa es muy baja
        $available = $this->getAvailableQuantityInPantry($pantry);
        
        return $available < ($this->quantity * 0.5); // Menos del 50% de lo necesario
    }
}
