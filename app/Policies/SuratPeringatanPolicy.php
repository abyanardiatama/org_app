<?php

namespace App\Policies;

use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratPeringatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Ketua dan sekretaris bisa melihat semua
        if (in_array($user->role, ['ketua','wakil', 'sekretaris', 'anggota', 'bsomtq', 'phkmi', 'bendahara'])) {
            return true;
        }

        // User biasa hanya bisa melihat surat peringatan yang ditujukan kepada mereka
        return false; // Filtering handled in the query
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SuratPeringatan $suratPeringatan): bool
    {
        // Ketua dan sekretaris bisa melihat semua
        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return true;
        }

        // User biasa hanya bisa melihat surat peringatan yang ditujukan kepada mereka
        return $suratPeringatan->penerima === $user->name || $suratPeringatan->nim_penerima === $user->nim;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya sekretaris dan ketua yang bisa membuat
        return in_array($user->role, ['sekretaris', 'ketua']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SuratPeringatan $suratPeringatan): bool
    {
        // Sekretaris dan ketua bisa mengedit
        return in_array($user->role, ['sekretaris', 'ketua']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratPeringatan $suratPeringatan): bool
    {
        // Sekretaris dan ketua bisa menghapus
        return in_array($user->role, ['sekretaris', 'ketua']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuratPeringatan $suratPeringatan): bool
    {
        // Sekretaris bisa memulihkan
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuratPeringatan $suratPeringatan): bool
    {
        // Sekretaris bisa menghapus permanen
        return true;
    }
}
