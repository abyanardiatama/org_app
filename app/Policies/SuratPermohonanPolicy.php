<?php

namespace App\Policies;

use App\Models\SuratPermohonan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratPermohonanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SuratPermohonan $suratPermohonan): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SuratPermohonan $suratPermohonan): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratPermohonan $suratPermohonan): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuratPermohonan $suratPermohonan): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuratPermohonan $suratPermohonan): bool
    {
        return true;
    }
}
