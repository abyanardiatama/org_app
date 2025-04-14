<?php

namespace App\Filament\Resources\SpkbResource\Pages;

use App\Filament\Resources\SpkbResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpkb extends EditRecord
{
    protected static string $resource = SpkbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
