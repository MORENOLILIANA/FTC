<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PantryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pantry_id',
        'product_id',
        'user_id',
        'quantity',
        'unit',
        'expiry_date',
        'location',
        'notes',
        'minimum_quantity'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'float',
        'minimum_quantity' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con la despensa
     */
    public function pantry()
    {
        return $this->belongsTo(Pantry::class);
    }

    /**
     * Relación con el producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si el item está próximo a caducar
     */
    public function isExpiringSoon($days = 7): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->lessThanOrEqualTo(now()->addDays($days));
    }

    /**
     * Verificar si el item está caducado
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->lessThan(now());
    }

    /**
     * Verificar si el stock es bajo
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_quantity;
    }

    /**
     * Obtener días hasta la caducidad (también accesible como $item->days_until_expiry)
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expiry_date) {
            return -1;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Obtener estado del item
     */
    public function getStatusAttribute(): string
    {
        if ($this->isExpired()) {
            return 'expired';
        }

        if ($this->isExpiringSoon()) {
            return 'expiring_soon';
        }

        if ($this->isLowStock()) {
            return 'low_stock';
        }

        return 'normal';
    }

    /**
     * Obtener color del estado
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'expired' => '#dc3545',      // Rojo
            'expiring_soon' => '#ffc107', // Amarillo
            'low_stock' => '#fd7e14',     // Naranja
            'normal' => '#28a745',        // Verde
        ];

        return $colors[$this->status] ?? '#6c757d';
    }

    /**
     * Scope para items próximos a caducar
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now());
    }

    /**
     * Scope para items caducados
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    /**
     * Scope para items con stock bajo
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_quantity');
    }

    /**
     * Obtener valor nutricional total
     */
    public function getTotalNutritionalInfo(): array
    {
        if (!$this->product) {
            return [];
        }

        $multiplier = $this->quantity / 100; // Asumiendo que los valores son por 100g

        return [
            'calories' => ($this->product->calories_per_100g ?? 0) * $multiplier,
            'proteins' => ($this->product->proteins_per_100g ?? 0) * $multiplier,
            'carbs' => ($this->product->carbs_per_100g ?? 0) * $multiplier,
            'fats' => ($this->product->fats_per_100g ?? 0) * $multiplier,
            'fiber' => ($this->product->fiber_per_100g ?? 0) * $multiplier,
            'sugar' => ($this->product->sugar_per_100g ?? 0) * $multiplier,
            'salt' => ($this->product->salt_per_100g ?? 0) * $multiplier,
        ];
    }
}
