<?php

namespace App\Filament\Resources\SpkbResource\Pages;

use App\Filament\Resources\SpkbResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpkbs extends ListRecords
{
    protected static string $resource = SpkbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
