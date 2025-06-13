<?php

namespace App\Filament\Resources\DonasiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DonasiResource;
use App\Filament\Resources\DonasiResource\Widgets\DonasiOverview;
use App\Filament\Resources\DonasiResource\Widgets\KegiatanDonasiOverview;
use App\Filament\Resources\DonasiResource\Widgets\DonasiWidget;
use App\Models\Donasi;
use Illuminate\Support\Facades\Auth;

class ListDonasis extends ListRecords
{
    protected static string $resource = DonasiResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        $user = Auth::user();
        return array_filter([
            DonasiOverview::class,
            // Tampilkan KegiatanDonasiOverview hanya untuk user eksternal (bukan ketua/sekretaris)
            !in_array($user->role, ['ketua', 'sekretaris']) ? KegiatanDonasiOverview::class : null,
        ]);
    }
}
