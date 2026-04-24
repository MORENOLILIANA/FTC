<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get all users
     */
    public function getAllUsers(): Collection
    {
        return User::select('id', 'name', 'email', 'created_at', 'updated_at')
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?User
    {
        return User::select('id', 'name', 'email', 'created_at', 'updated_at')
                  ->find($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): ?User
    {
        $user = User::find($id);
        
        if (!$user) {
            return null;
        }
        
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
        
        return $user;
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): bool
    {
        $user = User::find($id);
        
        if (!$user) {
            return false;
        }
        
        return $user->delete();
    }

    /**
     * Get users with pagination
     */
    public function getUsersWithPagination(int $perPage = 10): LengthAwarePaginator
    {
        return User::select('id', 'name', 'email', 'created_at', 'updated_at')
                  ->orderBy('created_at', 'desc')
                  ->paginate($perPage);
    }
}
