<?php

namespace App\Filament\Resources\SertijabResource\Pages;

use App\Filament\Resources\SertijabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSertijabs extends ListRecords
{
    protected static string $resource = SertijabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
