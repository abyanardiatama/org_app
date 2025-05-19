<?php

namespace App\Filament\Resources\SKKKMIResource\Pages;

use App\Filament\Resources\SKKKMIResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSKKKMIS extends ListRecords
{
    protected static string $resource = SKKKMIResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
