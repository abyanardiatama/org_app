<?php

namespace App\Filament\Resources\SertijabResource\Pages;

use App\Filament\Resources\SertijabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSertijab extends EditRecord
{
    protected static string $resource = SertijabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
