<?php

namespace App\Filament\Resources\PerlombaanResource\Pages;

use App\Filament\Resources\PerlombaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerlombaans extends ListRecords
{
    protected static string $resource = PerlombaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
