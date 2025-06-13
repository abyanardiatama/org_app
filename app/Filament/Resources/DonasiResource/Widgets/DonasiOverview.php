<?php

namespace App\Filament\Resources\DonasiResource\Widgets;

use App\Models\Donasi;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class DonasiOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 2;
    protected function getStats(): array
    {
        $user = Auth::user();

        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            // Tampilkan total donasi seluruh user
            $totalDonasi = \App\Models\Donasi::sum('jumlah_donasi');
            $label = 'Total Donasi Semua Kegiatan';
            $desc = 'Total donasi seluruh user dan kegiatan';
        } else {
            // Tampilkan total donasi user sendiri
            $totalDonasi = \App\Models\Donasi::where('user_id', $user->id)->sum('jumlah_donasi');
            $label = 'Total Donasi';
            $desc = 'Total donasi Anda berdasarkan kegiatan yang didukung';
        }

        $totalDonasiFormatted = 'Rp ' . number_format($totalDonasi, 0, ',', '.');

        return [
            Stat::make($label, $totalDonasiFormatted)
                ->description($desc)
                ->color('success'),
        ];
    }
    protected function getColumns(): int
    {
        return 2;
    }
}
