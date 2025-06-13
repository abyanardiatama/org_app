<?php

namespace App\Policies;

use App\Models\MahasiswaBerprestasi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MahasiswaBerprestasiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only users with the 'ketua', 'sekretaris', or 'bendahara' roles can view any models
        return in_array($user->role, ['bsomtq']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MahasiswaBerprestasi $mahasiswaBerprestasi): bool
    {
        // Users with the 'ketua', 'sekretaris', 'bendahara', or 'bsomtq' roles can view the model
        return in_array($user->role, ['bsomtq']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Users with the or 'bsomtq' roles can create models
        return in_array($user->role, ['bsomtq']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MahasiswaBerprestasi $mahasiswaBerprestasi): bool
    {
        // Users with the or 'bsomtq' roles can update the model
        return in_array($user->role, ['bsomtq']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MahasiswaBerprestasi $mahasiswaBerprestasi): bool
    {
        // Users with the or 'bsomtq' roles can delete the model
        return in_array($user->role, ['bsomtq']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MahasiswaBerprestasi $mahasiswaBerprestasi): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MahasiswaBerprestasi $mahasiswaBerprestasi): bool
    {
        return true;
    }
}
