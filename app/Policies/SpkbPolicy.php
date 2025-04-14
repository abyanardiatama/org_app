<?php

namespace App\Policies;

use App\Models\User;
use App\Models\spkb;
use Illuminate\Auth\Access\Response;

class SpkbPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id !== null;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, spkb $spkb): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id !== null;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id !== null;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, spkb $spkb): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id !== null;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, spkb $spkb): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id !== null;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, spkb $spkb): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, spkb $spkb): bool
    {
        return true;
    }
}
