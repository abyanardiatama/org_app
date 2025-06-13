<?php

namespace App\Filament\Resources\PresensiResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Presensi;

class PresensiOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        // dd($userId); // Debugging line to check the user ID
        $totalPoin = Presensi::where('user_id', $userId)->sum('total_poin');
        // dd($totalPoin); // Debugging line to check the total poin

        return [
            Stat::make('Total Poin', $totalPoin)
                ->description('Total poin Anda berdasarkan presensi')
                ->color('success'),
        ];
    }
}
