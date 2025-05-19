<?php

namespace App\Filament\Resources\SuratKeteranganAktifResource\Pages;

use App\Filament\Resources\SuratKeteranganAktifResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratKeteranganAktifs extends ListRecords
{
    protected static string $resource = SuratKeteranganAktifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
