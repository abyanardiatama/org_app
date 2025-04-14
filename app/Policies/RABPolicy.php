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
        // Ketua dapat melihat semua data
        if ($user->role === 'ketua') {
            return true;
        }

        // Bendahara dapat melihat semua data
        if ($user->role === 'bendahara') {
            return true;
        }

        // PH KMI hanya dapat melihat data yang mereka buat
        if ($user->role === 'ph_kmi') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RAB $rAB): bool
    {
        // Ketua dapat melihat semua data
        if ($user->role === 'ketua') {
            return true;
        }

        // Bendahara dapat melihat semua data
        if ($user->role === 'bendahara') {
            return true;
        }

        // PH KMI hanya dapat melihat data yang mereka buat
        if ($user->role === 'ph_kmi' && $rAB->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'bendahara';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RAB $rAB): bool
    {
        return $user->role === 'bendahara';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RAB $rAB): bool
    {
        return $user->role === 'bendahara';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RAB $rAB): bool
    {
        return in_array($user->role, ['ketua', 'sekretaris', 'bendahara']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RAB $rAB): bool
    {
        return in_array($user->role, ['ketua', 'sekretaris', 'bendahara']);
    }
}
