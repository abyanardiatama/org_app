<?php

namespace App\Filament\Resources\LPJResource\Pages;

use App\Filament\Resources\LPJResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLPJ extends EditRecord
{
    protected static string $resource = LPJResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
