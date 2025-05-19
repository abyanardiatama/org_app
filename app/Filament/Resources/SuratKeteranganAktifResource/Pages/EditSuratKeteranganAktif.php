<?php

namespace App\Filament\Resources\SuratKeteranganAktifResource\Pages;

use App\Filament\Resources\SuratKeteranganAktifResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratKeteranganAktif extends EditRecord
{
    protected static string $resource = SuratKeteranganAktifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
