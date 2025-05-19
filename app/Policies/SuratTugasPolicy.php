<?php

namespace App\Policies;

use App\Models\SuratTugas;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratTugasPolicy
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
    public function view(User $user, SuratTugas $suratTugas): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return $suratTugas->kepada === $user->name || $suratTugas->nim_kepada === $user->nim;
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
    public function update(User $user, SuratTugas $suratTugas): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratTugas $suratTugas): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuratTugas $suratTugas): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuratTugas $suratTugas): bool
    {
        return true;
    }
}
