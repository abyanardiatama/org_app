<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Presensi;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class PresensiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
     use HandlesAuthorization;
    public function viewAny(User $user): bool
    {
        if (Auth::check()){
            return true;
        }
        else{
            return false;
        }

        // return in_array($user->role, ['ketua', 'sekretaris']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Presensi $presensi): bool
    {
        // if (Auth::check()){
        //     return true;
        // }
        // else{
        //     return false;
        // }
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat presensi mereka sendiri
        return $user->id === $presensi->user_id;
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
    public function update(User $user, Presensi $presensi): bool
    {
        // if (Auth::check()){
        //     return true;
        // }
        // else{
        //     return false;
        // }
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat presensi mereka sendiri
        return $user->id === $presensi->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Presensi $presensi): bool
    {
        // if (Auth::check()){
        //     return true;
        // }
        // else{
        //     return false;
        // }
        return in_array($user->role, ['ketua', 'sekretaris']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Presensi $presensi): bool
    {
        if (Auth::check()){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Presensi $presensi): bool
    {
        if (Auth::check()){
            return true;
        }
        else{
            return false;
        }
    }
}
