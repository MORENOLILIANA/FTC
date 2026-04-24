<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Relación con las despensas del usuario
     */
    public function pantries()
    {
        return $this->hasMany(Pantry::class);
    }

    /**
     * Relación con las listas de compra del usuario
     */
    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class);
    }

    /**
     * Relación con las despensas compartidas
     */
    public function sharedPantries()
    {
        return $this->belongsToMany(Pantry::class, 'pantry_users')
            ->withPivot('permission')
            ->withTimestamps();
    }

    /**
     * Relación con las listas de compra compartidas
     */
    public function sharedShoppingLists()
    {
        return $this->belongsToMany(ShoppingList::class, 'shopping_list_users')
            ->withPivot('permission')
            ->withTimestamps();
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Verificar si el usuario tiene permiso en una despensa
     */
    public function hasPantryPermission(Pantry $pantry, string $permission): bool
    {
        if ($pantry->user_id === $this->id) {
            return true; // Dueño tiene todos los permisos
        }

        $sharedPantry = $this->sharedPantries()
            ->where('pantry_id', $pantry->id)
            ->where('permission', $permission)
            ->first();

        return $sharedPantry !== null;
    }
}
