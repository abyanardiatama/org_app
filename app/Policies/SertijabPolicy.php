<?php

namespace App\Policies;

use App\Models\Sertijab;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SertijabPolicy
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
    public function view(User $user, Sertijab $sertijab): bool
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
    public function update(User $user, Sertijab $sertijab): bool
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
    public function delete(User $user, Sertijab $sertijab): bool
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
    public function restore(User $user, Sertijab $sertijab): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sertijab $sertijab): bool
    {
        return true;
    }
}
