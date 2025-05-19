<?php

namespace App\Filament\Resources\SuratDivisiKKMIResource\Pages;

use App\Filament\Resources\SuratDivisiKKMIResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratDivisiKKMI extends EditRecord
{
    protected static string $resource = SuratDivisiKKMIResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
