<?php

namespace App\Filament\Resources\SKKKMIResource\Pages;

use App\Filament\Resources\SKKKMIResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSKKKMI extends EditRecord
{
    protected static string $resource = SKKKMIResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
