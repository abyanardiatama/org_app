<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Kegiatan;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class KegiatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // in_array(Auth::user()->role, ['ketua', 'sekretaris'])
        if  (Auth::user()->role == 'ketua' || Auth::user()->role == 'sekretaris'){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kegiatan $kegiatan): bool
    {
        if (Auth::user()->role == 'ketua' || Auth::user()->role == 'sekretaris'){
            return true;
        }
        else{
            return false;
        }
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
    public function update(User $user, Kegiatan $kegiatan): bool
    {
        if (Auth::user()->role == 'ketua' || Auth::user()->role == 'sekretaris'){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kegiatan $kegiatan): bool
    {
        if (Auth::user()->role == 'ketua' || Auth::user()->role == 'sekretaris'){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kegiatan $kegiatan): bool
    {
        if (Auth::user()->role == 'ketua' || Auth::user()->role == 'sekretaris'){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kegiatan $kegiatan): bool
    {
        if (Auth::user()->role == 'ketua' || Auth::user()->role == 'sekretaris'){
            return true;
        }
        else{
            return false;
        }
    }
}
