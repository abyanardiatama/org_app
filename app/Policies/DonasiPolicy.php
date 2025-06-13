<?php

namespace App\Policies;

use App\Models\Donasi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DonasiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['external','ketua', 'sekretaris', 'bendahara'])) {
            return true; // External users can view any Donasi
        }
        return false; // Other roles cannot view any Donasi
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Donasi $donasi): bool
    {
        if (in_array($user->role, [ 'external','ketua', 'sekretaris', 'bendahara'])) {
            return true; // External users can view any Donasi
        }
        return false; // Other roles cannot view Donasi
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris', 'bendahara', 'external'])) {
            return true; // Only Ketua, Sekretaris, and Bendahara can create Donasi
        }
        return false; // Other roles cannot create Donasi
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Donasi $donasi): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris', 'bendahara', 'external'])) {
            return true; // Only Ketua, Sekretaris, and Bendahara can update Donasi
        }
        return false; // Other roles cannot update Donasi
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Donasi $donasi): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris', 'bendahara', 'external'])) {
            return true; // Only Ketua, Sekretaris, and Bendahara can delete Donasi
        }
        return false; // Other roles cannot delete Donasi
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Donasi $donasi): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris', 'bendahara'])) {
            return true; // Only Ketua, Sekretaris, and Bendahara can restore Donasi
        }
        return false; // Other roles cannot restore Donasi
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Donasi $donasi): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris', 'bendahara'])) {
            return true; // Only Ketua, Sekretaris, and Bendahara can permanently delete Donasi
        }
        return false; // Other roles cannot permanently delete Donasi
    }
}
