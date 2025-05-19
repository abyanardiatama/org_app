<?php

namespace App\Filament\Resources\LPJResource\Pages;

use App\Filament\Resources\LPJResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLPJS extends ListRecords
{
    protected static string $resource = LPJResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
