<?php

namespace App\Policies;

use App\Models\Perlombaan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PerlombaanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['bsomtq'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Perlombaan $perlombaan): bool
    {
        if (in_array($user->role, ['bsomtq'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (in_array($user->role, ['bsomtq'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Perlombaan $perlombaan): bool
    {
        if (in_array($user->role, ['bsomtq'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Perlombaan $perlombaan): bool
    {
        if (in_array($user->role, ['bsomtq'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Perlombaan $perlombaan): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Perlombaan $perlombaan): bool
    {
        return true;
    }
}
