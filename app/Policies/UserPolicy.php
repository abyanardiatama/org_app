<?php

namespace App\Policies;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->role === 'external') {
            return false;
        }
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        //hanya ketua dan sekretaris yang bisa melihat semua user, selain itu hanya bisa melihat user dengan divisi yang sama
        return $user->where('divisi_id', $user->divisi_id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->role === 'external') {
            return false;
        }
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        
        return $user->where('divisi_id', $user->divisi_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        
        return $user->divisi_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        //ketua dan sekretaris bisa update semua user
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        //lainnya tidak bisa
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        
        return false;
    }
}
