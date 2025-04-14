<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Kegiatan;
use App\Models\Presensi;

class KegiatanObserver
{
    /**
     * Handle the Kegiatan "created" event.
     */
    public function created(Kegiatan $kegiatan): void
    {
        // Ambil semua pengguna
        $users = User::all();

        foreach ($users as $user) {
            Presensi::create([
                'user_id' => $user->id,
                'kegiatan_id' => $kegiatan->id,
                'status' => 'pending', // Admin tinggal mengisi Hadir/Tidak Hadir
                'poin_peran' => 1, // Bisa diatur sesuai kebutuhan
                'poin_kehadiran' => -1, // Bisa diatur sesuai kebutuhan
                //didapat dari total point sebelumnya
                'total_poin' => $user->presensi->sum('total_poin')
            ]);
        }
    }

    /**
     * Handle the Kegiatan "updated" event.
     */
    public function updated(Kegiatan $kegiatan): void
    {
        //
    }

    /**
     * Handle the Kegiatan "deleted" event.
     */
    public function deleted(Kegiatan $kegiatan): void
    {
        //
    }

    /**
     * Handle the Kegiatan "restored" event.
     */
    public function restored(Kegiatan $kegiatan): void
    {
        //
    }

    /**
     * Handle the Kegiatan "force deleted" event.
     */
    public function forceDeleted(Kegiatan $kegiatan): void
    {
        //
    }
}
