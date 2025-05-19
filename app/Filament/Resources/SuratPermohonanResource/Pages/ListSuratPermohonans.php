<?php

namespace App\Filament\Resources\SuratPermohonanResource\Pages;

use App\Filament\Resources\SuratPermohonanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratPermohonans extends ListRecords
{
    protected static string $resource = SuratPermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
