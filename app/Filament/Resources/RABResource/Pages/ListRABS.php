<?php

namespace App\Filament\Resources\RABResource\Pages;

use App\Filament\Resources\RABResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRABS extends ListRecords
{
    protected static string $resource = RABResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
