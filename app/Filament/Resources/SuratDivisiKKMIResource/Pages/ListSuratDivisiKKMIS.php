<?php

namespace App\Filament\Resources\SuratDivisiKKMIResource\Pages;

use App\Filament\Resources\SuratDivisiKKMIResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratDivisiKKMIS extends ListRecords
{
    protected static string $resource = SuratDivisiKKMIResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
