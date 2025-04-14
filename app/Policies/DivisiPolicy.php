<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class DivisiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['ketua'])) {
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
    public function view(User $user, Divisi $divisi): bool
    {
        if (in_array($user->role, ['ketua'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id === $divisi->id;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (Auth::user()->role == 'ketua' || Auth::user()->role == 'sekretaris'){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Divisi $divisi): bool
    {
        if (in_array($user->role, ['ketua'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id === $divisi->id;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Divisi $divisi): bool
    {
        if (in_array($user->role, ['ketua'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id === $divisi->id;
        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Divisi $divisi): bool
    {
        if (in_array($user->role, ['ketua'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id === $divisi->id;

        //user lain tidak bisa
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Divisi $divisi): bool
    {
        if (in_array($user->role, ['ketua'])) {
            return true;
        }

        // User biasa hanya bisa melihat divisi mereka sendiri
        // return $user->divisi_id === $divisi->id;

        //user lain tidak bisa
        return false;
    }
}
