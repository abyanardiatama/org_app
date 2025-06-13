<?php

namespace App\Policies;

use App\Models\RAB;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class RABPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris', 'bendahara', 'phkmi']))  {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RAB $rAB): bool
    {
        if (in_array($user->role, ['ketua', 'bendahara', 'sekretaris', 'phkmi'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['ketua', 'sekretaris', 'bendahara','phkmi']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RAB $rAB): bool
    {
        return in_array($user->role, ['ketua', 'sekretaris', 'bendahara','phkmi']);
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RAB $rAB): bool
    {
        return in_array($user->role, ['ketua', 'sekretaris', 'bendahara','phkmi']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RAB $rAB): bool
    {
        // This method is not used in the current context, but you can implement it if needed
        return in_array($user->role, ['ketua', 'sekretaris', 'bendahara']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RAB $rAB): bool
    {
        // This method is not used in the current context, but you can implement it if needed
        return in_array($user->role, ['ketua', 'sekretaris', 'bendahara']);
    }
}
