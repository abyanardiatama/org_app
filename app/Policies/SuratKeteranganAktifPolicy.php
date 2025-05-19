<?php

namespace App\Policies;

use App\Models\SuratKeteranganAktif;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratKeteranganAktifPolicy
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
    public function view(User $user, SuratKeteranganAktif $suratKeteranganAktif): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return $suratKeteranganAktif->kepada === $user->name || $suratKeteranganAktif->nim_kepada === $user->nim;
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
    public function update(User $user, SuratKeteranganAktif $suratKeteranganAktif): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratKeteranganAktif $suratKeteranganAktif): bool
    {
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuratKeteranganAktif $suratKeteranganAktif): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuratKeteranganAktif $suratKeteranganAktif): bool
    {
        return true;
    }
}
