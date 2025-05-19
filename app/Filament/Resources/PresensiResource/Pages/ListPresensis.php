<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PresensiResource;
use App\Filament\Resources\PresensiResource\Widgets\PresensiOverview;

class ListPresensis extends ListRecords
{
    protected static string $resource = PresensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            PresensiOverview::class,
        ];
    }
}
