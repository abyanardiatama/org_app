<?php

namespace App\Policies;

use App\Models\SuratUndangan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratUndanganPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['ketua','wakil', 'sekretaris', 'anggota', 'bsomtq', 'phkmi', 'bendahara'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SuratUndangan $suratUndangan): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return $suratUndangan->nama_ketupel === $user->name;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SuratUndangan $suratUndangan): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratUndangan $suratUndangan): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuratUndangan $suratUndangan): bool
    {
        return True;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuratUndangan $suratUndangan): bool
    {
        return True;
    }
}
